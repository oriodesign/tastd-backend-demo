<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\RestaurantNotFoundException;

/**
 * Class RestaurantRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class RestaurantRepository extends BaseEntityRepository
{
    /**
     * @param integer $id
     *
     * @return Restaurant
     * @throws RestaurantNotFoundException
     */
    public function get($id)
    {
        $restaurant = $this->find($id);
        if (!$restaurant) {
            throw new RestaurantNotFoundException();
        }

        return $restaurant;
    }

    /**
     * @return integer
     */
    public function countAll()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(r)')
            ->from(Restaurant::SHORTCUT_CLASS_NAME, 'r')
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return mixed
     */
    public function count($from, $to)
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('DATE(r.created) as day, COUNT(r) as counter')
            ->groupBy('day')
            ->from(Restaurant::SHORTCUT_CLASS_NAME, 'r')
            ->where('r.created > :from')
            ->andWhere('r.created < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     * @param string $lat
     * @param string $lng
     *
     * @return boolean
     */
    public function existsWithCoordinatesAndName($name, $lat, $lng)
    {
        $lat = (string) $lat;
        $lng = (string) $lng;

        $results = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(Restaurant::SHORTCUT_CLASS_NAME, 'r')
            ->where('r.lat = :lat')
            ->andWhere('r.lng = :lng')
            ->andWhere('r.name = :name')
            ->setParameter('name', $name)
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
            ->getQuery()
            ->getResult();

        return count($results) > 0;
    }

    /**
     * @param $name
     * @param $lat
     * @param $lng
     * @param $id
     *
     * @return bool
     */
    public function existsWithSimilarCoordinatesAndName($name, $lat, $lng, $id = null)
    {
        $lat = (string) $lat;
        $lng = (string) $lng;



        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r.id')
            ->addSelect('DISTANCE(r.lat,r.lng,:lat,:lng) AS HIDDEN distance')
            ->from(Restaurant::SHORTCUT_CLASS_NAME, 'r')
            ->andWhere('r.name = :name')
            ->having('distance < 0.5')
            ->setParameter('name', $name)
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng);


        if ($id) {
            $queryBuilder
                ->andWhere('r.id <> :id')
                ->setParameter('id', $id);
        }

        $results = $queryBuilder->getQuery()
            ->getArrayResult();

        return count($results) > 0;
    }

    /**
     * @param Restaurant $restaurant
     *
     * @return Restaurant|null
     */
    public function getRestaurantByRestaurant(Restaurant $restaurant)
    {
        $results = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(Restaurant::SHORTCUT_CLASS_NAME, 'r')
            ->where('r.name = :name')
            ->andWhere('r.lat = :lat')
            ->andWhere('r.lng = :lng')
            ->setParameter('name', $restaurant->getName())
            ->setParameter('lat', $restaurant->getLat())
            ->setParameter('lng', $restaurant->getLng())
            ->getQuery()
            ->getResult();

        if (count($results)>0) {
            return $results[0];
        }

        return null;
    }

    /**
     * @param string $ids
     * @return array
     */
    public function getByIds($ids)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(Restaurant::SHORTCUT_CLASS_NAME, 'r')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', explode(',', $ids));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getRestaurantsPager($request)
    {
        $page = $request->query->get('page', 1);
        $geoname = $request->query->get('geoname');
        $name = $request->query->get('name');
        $cuisine = $request->query->get('cuisine');
        $orderBy = $request->query->get('orderBy');
        $averageCost = $request->query->get('averageCost');
        $maxDistance = $request->query->get('maxDistance');
        $lat = $request->query->get('lat');
        $lng = $request->query->get('lng');

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(Restaurant::SHORTCUT_CLASS_NAME, 'r');

        if ($name) {
            $queryBuilder
                ->andWhere('r.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        if ($geoname) {
            $queryBuilder
                ->andWhere('r.geoname = :geoname')
                ->setParameter('geoname', $geoname);
        }

        if ($cuisine === 'not_null') {
            $queryBuilder
                ->andWhere('r.cuisine IS NOT NULL');
        }

        if ($averageCost === 'not_null') {
            $queryBuilder
                ->andWhere('r.averageCost IS NOT NULL');
        }

        if ($orderBy === 'score') {
            $queryBuilder
                ->leftJoin('r.reviews', 'w')
                ->addGroupBy('r.id')
                ->addSelect('COUNT(w.id) AS HIDDEN frequency')
                ->orderBy('frequency', 'DESC');
        }

        if ($orderBy === 'distance') {
            $queryBuilder
                ->addSelect('DISTANCE(r.lat,r.lng,:lat,:lng) AS HIDDEN distance')
                ->setParameter('lat', $lat)
                ->setParameter('lng', $lng)
                ->orderBy('distance', 'ASC');

            if ($maxDistance) {
                $queryBuilder
                    ->andHaving('distance < :maxDistance')
                    ->setParameter('maxDistance', $maxDistance);
            }

        }

        return $this->getPager($queryBuilder, $page);
    }
}