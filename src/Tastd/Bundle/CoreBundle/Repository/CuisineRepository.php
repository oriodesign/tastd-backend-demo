<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Cuisine;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\Review;

/**
 * Class CuisineRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class CuisineRepository extends BaseEntityRepository
{

    /**
     * Return cuisine with name Other or the first cuisine available
     *
     * @return Cuisine
     */
    public function getDefaultCuisine()
    {

        $defaultCuisines = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Cuisine::SHORTCUT_CLASS_NAME, 'c')
            ->where('c.name = :name')
            ->setParameter('name', 'Other')
            ->getQuery()
            ->getResult();

        if (count($defaultCuisines) > 0) {
            return $defaultCuisines[0];
        }
        $cuisines = $this->findAll();

        return $cuisines[0];
    }

    /**
     * @param string $name
     *
     * @return null|Cuisine
     */
    public function getCuisineByName($name)
    {
        $cuisines = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Cuisine::SHORTCUT_CLASS_NAME, 'c')
            ->where('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
        if (count($cuisines) > 0) {
            return $cuisines[0];
        }

        return null;
    }

    /**
     * @param $c1
     * @param $c2
     *
     * @return array
     */
    protected function mergeCuisinesResults($c1, $c2)
    {
        $ids = array();
        $results = array();
        foreach ($c1 as $cuisine) {
            $ids[] = $cuisine->getId();
            $results[] = $cuisine;
        }
        foreach ($c2 as $cuisine){
            if (!in_array($cuisine->getId(), $ids)) {
                $results[] = $cuisine;
                $ids[] = $cuisine->getId();
            }
        }

        $this->sortCuisineByName($results);

        return $results;
    }

    protected function getAllAlphabeticalFromReviewsAndWishes(Request $request)
    {
        $users = $request->query->get('users');
        $geoname = $request->query->get('geoname');
        $leadersOf = $request->query->get('leadersOf');

        $wishRequest = new Request();
        $wishRequest->query->set('geoname', $geoname);
        $wishRequest->query->set('leadersOf', $leadersOf);
        $wishRequest->query->set('users', $users);
        $wishRequest->query->set('wish', 'true');
        $wishCuisines = $this->getAllAlphabetical($wishRequest);

        $reviewsRequest = new Request();
        $reviewsRequest->query->set('geoname', $geoname);
        $reviewsRequest->query->set('leadersOf', $leadersOf);
        $reviewsRequest->query->set('users', $users);
        $reviewsCuisines = $this->getAllAlphabetical($reviewsRequest);

        return $this->mergeCuisinesResults($wishCuisines, $reviewsCuisines);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAllAlphabetical(Request $request)
    {
        $queryBuilder = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Cuisine::SHORTCUT_CLASS_NAME, 'c');

        return $queryBuilder
            ->orderBy('c.name')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return integer
     */
    public function countAll()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(c)')
            ->from(Cuisine::SHORTCUT_CLASS_NAME, 'c')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return array
     */
    public function getTopRankedCuisines()
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('c.id, c.name, COUNT(c) as counter')
            ->groupBy('re.cuisine')
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->leftJoin('r.restaurant', 're')
            ->leftJoin('re.cuisine', 'c')
            ->orderBy('counter', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Restaurant $restaurant
     *
     * @return Proxy
     */
    public function getMostUsedCuisine(Restaurant $restaurant)
    {
        $result = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('c.id, COUNT(c) as counter')
            ->groupBy('r.cuisine')
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->leftJoin('r.cuisine', 'c')
            ->where('r.restaurant = :restaurant')
            ->setParameter('restaurant', $restaurant)
            ->orderBy('counter', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        return $this->getEntityManager()->getReference(Cuisine::CLASS_NAME, $result['id']);
    }


    /**
     * @param array $cuisines
     */
    protected function sortCuisineByName(&$cuisines)
    {
        usort($cuisines, function(Cuisine $a, Cuisine $b){
            return strcasecmp($a->getName(), $b->getName());
        });
    }

}