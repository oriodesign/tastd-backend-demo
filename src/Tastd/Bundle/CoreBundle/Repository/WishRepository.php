<?php

namespace Tastd\Bundle\CoreBundle\Repository;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\WishNotFoundException;

/**
 * Class WishRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class WishRepository extends BaseEntityRepository
{

    protected $wishesBag;

    /**
     * @param integer $id
     *
     * @return Wish
     * @throws WishNotFoundException
     */
    public function get($id)
    {
        $wish = $this->find($id);
        if (!$wish) {
            throw new WishNotFoundException();
        }

        return $wish;
    }

    /**
     * @param $userId
     *
     * @return array
     */
    public function getWishesIdsOf($userId)
    {
        if (isset($this->wishesBag[$userId])) {
            return $this->wishesBag[$userId];
        }

        $sql = 'SELECT wishes.restaurant_id AS id FROM wishes WHERE wishes.user_id = :user_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $wishesIds = array_reduce($results, function($carry, $item){
            $carry[] = $item['id'];
            return $carry;
        }, array());

        $this->wishesBag[$userId] = $wishesIds;

        return $wishesIds;
    }

    /**
     * @param User       $user
     * @param Restaurant $restaurant
     *
     * @return array
     */
    public function getAllByUserAndRestaurant(User $user, Restaurant $restaurant)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('w')
            ->from(Wish::SHORTCUT_CLASS_NAME, 'w')
            ->where('w.user = :user')
            ->andWhere('w.restaurant = :restaurant')
            ->setParameter('user', $user)
            ->setParameter('restaurant', $restaurant);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getAllWishesPager(Request $request)
    {
        $cuisine = $request->query->get('cuisine');
        $geoname = $request->query->get('geoname');
        $user = $request->query->get('user');
        $restaurant = $request->query->get('restaurant');
        $page = $request->query->get('page', 1);

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('w')
            ->from(Wish::SHORTCUT_CLASS_NAME, 'w');

        if ($cuisine) {
            $queryBuilder
                ->andWhere('w.cuisine = :cuisine')
                ->setParameter('cuisine', $cuisine);
        }

        if ($geoname) {
            $queryBuilder
                ->andWhere('w.geoname = :geoname')
                ->setParameter('geoname', $geoname);
        }

        if ($user) {
            $queryBuilder
                ->andWhere('w.user = :user')
                ->setParameter('user', $user);
        }

        if ($restaurant) {
            $queryBuilder
                ->andWhere('w.restaurant = :restaurant')
                ->setParameter('restaurant', $restaurant);
        }

        return $this->getPager($queryBuilder, $page);
    }

    /**
     * @param int    $userId
     * @param string $groupBy
     *
     * @return array
     */
    public function getExpertiseByUser($userId, $groupBy)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(
                'c.id AS cuisine_id',
                'c.name AS cuisine_name',
                'c.color AS cuisine_color',
                'g.id AS geoname_id',
                'g.asciiName as geoname_name'
            )
            ->from(Wish::SHORTCUT_CLASS_NAME, 'w')
            ->leftJoin('w.geoname', 'g')
            ->leftJoin('w.cuisine', 'c')
            ->where('w.user = :user')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('g.asciiName', 'ASC')
            ->setParameter('user', $userId);

        $results = $queryBuilder->getQuery()->getScalarResult();

        if ($groupBy === 'geoname') {
            $results = $this->remapByGeoname($results);
        }

        return $results;
    }

    /**
     * @param array $results
     *
     * @return array
     */
    private function remapByGeoname($results)
    {
        $expertise = array();
        foreach ($results as $result) {
            $this->updateGeonameExpertise($expertise, $result);
            $this->updateCuisineExpertise($expertise, $result);
        }
        return $this->removeKeysFromMappedByGeoname($expertise);
    }

    /**
     * @param $expertise
     * @param $result
     */
    private function updateGeonameExpertise(&$expertise, $result)
    {
        if (!(isset($expertise[$result['geoname_id']]))){
            $expertise[$result['geoname_id']] = array(
                'id' => (int) $result['geoname_id'],
                'name' => $result['geoname_name'],
                'count' => 1,
                'cuisines' => array()
            );
        } else {
            $expertise[$result['geoname_id']]['count']++;
        }
    }

    private function updateCuisineExpertise(&$expertise, $result)
    {
        if (!isset($expertise[$result['geoname_id']]['cuisines'][$result['cuisine_id']])) {
            $expertise[$result['geoname_id']]['cuisines'][$result['cuisine_id']] =  array(
                'id'=> (int) $result['cuisine_id'],
                'name'=>$result['cuisine_name'],
                'color' => $result['cuisine_color'],
                'count' => 1
            );
        } else {
            $expertise[$result['geoname_id']]['cuisines'][$result['cuisine_id']]['count']++;
        }
    }

    /**
     * @param $expertise
     * @return array
     */
    private function removeKeysFromMappedByGeoname($expertise)
    {
        $geonames = array();
        foreach ($expertise as $geoname) {
            $cuisines = array();
            foreach ($geoname['cuisines'] as $cuisine) {
                $cuisines[] = $cuisine;
            }
            $geoname['cuisines']  = $cuisines;
            $geonames[] = $geoname;
        }

        return $geonames;
    }


    /**
     * @param Request $request
     * @param array   $restaurantsIds
     *
     * @return array
     */
    public function getDataForFlags(Request $request, $restaurantsIds = array())
    {
        $geoname = $request->query->get('geoname');
        $users = $request->query->get('users');
        $cuisines = $request->query->get('cuisines');
        $orderBy = $request->query->get('orderBy');
        $wishedBy = $request->query->get('wishedBy');
        $reviewedBy = $request->query->get('reviewedBy');

        $maxLat = $request->query->get('maxLat');
        $maxLng = $request->query->get('maxLng');

        $minLat = $request->query->get('minLat');
        $minLng = $request->query->get('minLng');

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(
                'r.id AS restaurantId,
                r.lat AS lat,
                r.lng AS lng,
                r.name AS name,
                r.address AS address,
                c.color AS color,
                c.name AS cuisineName,
                c.id AS cuisineId,
                r.thumb AS picture')
            ->from(Wish::SHORTCUT_CLASS_NAME, 'w')
            ->leftJoin('w.restaurant', 'r')
            ->leftJoin('w.cuisine', 'c')
            ->leftJoin('w.user', 'u')
            ->groupBy('r.id');

        if ($wishedBy || $reviewedBy) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('r.id', ':reviewedBy'))
                ->setParameter('reviewedBy', $restaurantsIds);
        }

        if ($minLat && $minLng && $maxLat && $maxLng) {
            $queryBuilder
                ->andWhere('r.lng > :minLng')
                ->andWhere('r.lng < :maxLng')
                ->andWhere('r.lat > :minLat')
                ->andWhere('r.lat < :maxLat')
                ->setParameter('minLat', $minLat)
                ->setParameter('minLng', $minLng)
                ->setParameter('maxLat', $maxLat)
                ->setParameter('maxLng', $maxLng);
        } else if (isset($geoname)) {
            $queryBuilder
                ->andWhere('w.geoname = :geoname')
                ->setParameter('geoname', $geoname);
        }

        if (isset($users)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('u.id', ':users'))
                ->setParameter('users', explode(',', $users));
        }

        if (isset($cuisines)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('c.id', ':cuisines'))
                ->setParameter('cuisines', explode(',', $cuisines));
        }

        if ($orderBy === 'cuisine') {
            $queryBuilder->orderBy('c.name', 'ASC');
        } else {
            $queryBuilder->orderBy('r.name', 'ASC');
        }

        return $queryBuilder->getQuery()->getScalarResult();
    }

}