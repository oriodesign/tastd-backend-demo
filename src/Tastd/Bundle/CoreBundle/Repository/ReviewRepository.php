<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Pagerfanta;
use PDO;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Doctrine\Condition;
use Tastd\Bundle\CoreBundle\Doctrine\SqlBuilder;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\ReviewNotFoundException;

/**
 * Class ReviewRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class ReviewRepository extends BaseEntityRepository
{

    protected $reviewsBag;

    /**
     * @param integer $id
     *
     * @return Review
     * @throws ReviewNotFoundException
     */
    public function get($id)
    {
        $review = $this->find($id);
        if (!$review) {
            throw new ReviewNotFoundException();
        }

        return $review;
    }

    /**
     * @param $userId
     *
     * @return array
     */
    public function getReviewsIdsOf($userId)
    {
        if (isset($this->reviewsBag[$userId])) {
            return $this->reviewsBag[$userId];
        }

        $sql = 'SELECT reviews.restaurant_id AS id FROM reviews WHERE reviews.user_id = :user_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $reviewsIds = array_reduce($results, function($carry, $item){
            $carry[] = $item['id'];
            return $carry;
        }, array());

        $this->reviewsBag[$userId] = $reviewsIds;

        return $reviewsIds;
    }

    /**
     * @param User       $user
     * @param Restaurant $restaurant
     * @param int        $page
     *
     * @return Pagerfanta
     */
    public function getPagedLeadersReviews(User $user, Restaurant $restaurant, $page = 1)
    {
        $leadersId = $this->getLeadersOf($user->getId());
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder();
        $queryBuilder
            ->select('r,u,c')
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.cuisine', 'c')
            ->andWhere('r.restaurant = :restaurant')
            ->andWhere($queryBuilder->expr()->in('u.id', ':leadersId'))
            ->setParameter('restaurant', $restaurant)
            ->setParameter('leadersId', $leadersId);

        return  $this->getPager($queryBuilder, $page);
    }

    /**
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getAllReviewsPager(Request $request)
    {
        $cuisine = $request->query->get('cuisine');
        $geoname = $request->query->get('geoname');
        $user = $request->query->get('user');
        $restaurant = $request->query->get('restaurant');
        $minCost = $request->query->get('minCost');
        $maxCost = $request->query->get('maxCost');
        $orderBy = $request->query->get('orderBy');
        $users = $request->query->get('users');
        $tags = $request->query->get('tags');
        $page = $request->query->get('page', 1);
        $leadersOf = $request->query->get('leadersOf');

        if ($leadersOf) {
            $leadersId = $this->getLeadersOf($leadersOf);
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(Review::SHORTCUT_CLASS_NAME, 'r');

        if ($cuisine) {
            $queryBuilder
                ->andWhere('r.cuisine = :cuisine')
                ->setParameter('cuisine', $cuisine);
        }

        if ($geoname) {
            $queryBuilder
                ->andWhere('r.geoname = :geoname')
                ->setParameter('geoname', $geoname);
        }

        if ($user && isset($leadersId)) {
            $leadersId[] = $user;
            $queryBuilder
                ->leftJoin('r.user', 'u')
                ->andWhere($queryBuilder->expr()->in('u.id', ':leadersId'))
                ->setParameter('leadersId', $leadersId);
        } else if ($user){
            $queryBuilder
                ->andWhere('r.user = :user')
                ->setParameter('user', $user);
        } else if ($users) {
            $queryBuilder
                ->andWhere('r.user IN (:users)')
                ->setParameter('users', explode(',', $users));
        } else if (isset($leadersId)) {
            $queryBuilder
                ->leftJoin('r.user', 'u')
                ->andWhere($queryBuilder->expr()->in('u.id', ':leadersId'))
                ->setParameter('leadersId', $leadersId);
        }

        if ($restaurant) {
            $queryBuilder
                ->andWhere('r.restaurant = :restaurant')
                ->setParameter('restaurant', $restaurant);
        }

        if (isset($minCost)) {
            $queryBuilder
                ->andWhere('r.cost > :minCost')
                ->setParameter('minCost', $minCost);
        }

        if (isset($maxCost)) {
            $queryBuilder
                ->andWhere('r.cost < :maxCost')
                ->setParameter('maxCost', $maxCost);
        }

        if (isset($tags)) {
            $queryBuilder
                ->leftJoin('r.tags', 't')
                ->andWhere('t.name IN (:tags)')
                ->setParameter('tags', $tags);
        }

        if (isset($orderBy) && $orderBy === 'position') {
            $queryBuilder
                ->orderBy('r.position', 'ASC');
        }

        if (isset($orderBy) && $orderBy === 'cuisine') {
            $queryBuilder
                ->leftJoin('r.cuisine','c')
                ->orderBy('c.name', 'ASC')
                ->addOrderBy('r.position', 'ASC');
        }

        if (isset($orderBy) && $orderBy === 'created') {
            $queryBuilder
                ->orderBy('r.created', 'DESC');
        }



        return $this->getPager($queryBuilder, $page);
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
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->where('r.created > :from')
            ->andWhere('r.created < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('day', 'ASC')
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
            ->select('COUNT(r)')
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->getQuery()
            ->getSingleScalarResult();
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
            ->select('r')
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->where('r.user = :user')
            ->andWhere('r.restaurant = :restaurant')
            ->setParameter('user', $user)
            ->setParameter('restaurant', $restaurant);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Request $request
     * @param array $restaurantsIds
     *
     * @return array
     */
    public function getDataForFlags(Request $request, $restaurantsIds = array())
    {
        $geoname = $request->query->get('geoname');
        $users = $request->query->get('users');
        $leadersOf = $request->query->get('leadersOf');
        $cuisines = $request->query->get('cuisines');
        $minCost = $request->query->get('minCost');
        $maxCost = $request->query->get('maxCost');
        $tags = $request->query->get('tags');
        $wishedBy = $request->query->get('wishedBy');
        $reviewedBy = $request->query->get('reviewedBy');

        $maxLat = $request->query->get('maxLat');
        $maxLng = $request->query->get('maxLng');

        $minLat = $request->query->get('minLat');
        $minLng = $request->query->get('minLng');

        if ($leadersOf) {
            $leadersId = $this->getLeadersOf($leadersOf);
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(
                'r.id AS restaurantId,
                r.lat AS lat,
                r.lng AS lng,
                r.name AS name,
                r.address AS address,
                SUM(1/(1+w.position)) AS points,
                c.color AS color,
                c.name AS cuisineName,
                c.id AS cuisineId,
                r.thumb AS picture')
            ->from(Review::SHORTCUT_CLASS_NAME, 'w')
            ->leftJoin('w.restaurant', 'r')
            ->leftJoin('w.cuisine', 'c')
            ->leftJoin('w.user', 'u')
            ->groupBy('r.id');

        if ($wishedBy || $reviewedBy) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('r.id', ':reviewedBy'))
                ->setParameter('reviewedBy', $restaurantsIds);
        }

        if (isset($minCost)) {
            $queryBuilder
                ->andWhere('r.averageCost > :minCost')
                ->setParameter('minCost', $minCost);
        }

        if (isset($maxCost)) {
            $queryBuilder
                ->andWhere('r.averageCost < :maxCost')
                ->setParameter('maxCost', $maxCost);
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

        if (isset($tags)) {
            $queryBuilder
                ->leftJoin('w.tags', 't')
                ->andWhere($queryBuilder->expr()->in('t.id', ':tags'))
                ->setParameter('tags', explode(',', $tags));
        }

        if (isset($users)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('u.id', ':users'))
                ->setParameter('users', explode(',', $users));
        }

        if (isset($leadersId)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('u.id', ':leadersId'))
                ->setParameter('leadersId', $leadersId);
        }

        if (isset($cuisines)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('c.id', ':cuisines'))
                ->setParameter('cuisines', explode(',', $cuisines));
        }

        $queryBuilder->orderBy('points', 'DESC');

        return $queryBuilder->getQuery()->getScalarResult();
    }

    /**
     * @param Restaurant $restaurant
     *
     * @return integer
     */
    public function getAverageCost(Restaurant $restaurant)
    {
        $sql = 'SELECT AVG(cost) as averageCost FROM reviews WHERE restaurant_id = :restaurant_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('restaurant_id', $restaurant->getId());
        $stmt->execute();
        $results = $stmt->fetch();

        return (int)$results["averageCost"];
    }

    /**
     * @param User       $user
     * @param Review $review
     *
     * @return int
     */
    public function getMaxPosition(User $user, Review $review)
    {
        $cuisine = $review->getCuisine();
        $geoname = $review->getGeoname();

        $sql = '
          SELECT MAX(reviews.position) AS maxPosition
          FROM reviews
          WHERE cuisine_id = ?
          AND geoname_id = ?
          AND user_id = ?';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $cuisine->getId());
        $stmt->bindValue(2, $geoname->getId());
        $stmt->bindValue(3, $user->getId());
        $stmt->execute();
        $results = $stmt->fetch();

        if (null === $results["maxPosition"]) {
            return 0;
        }

        return (int) $results["maxPosition"];
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
                'g.asciiName as geoname_name',
                'g.lat as lat',
                'g.lng as lng',
                'r.score AS score'
            )
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->leftJoin('r.geoname', 'g')
            ->leftJoin('r.cuisine', 'c')
            ->where('r.user = :user')
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
                'lat' => $result['lat'],
                'lng' => $result['lng'],
                'name' => $result['geoname_name'],
                'score' => $result['score'],
                'count' => 1,
                'cuisines' => array()
            );
        } else {
            $expertise[$result['geoname_id']]['score'] += $result['score'];
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
                'score' => $result['score'],
                'count' => 1
            );
        } else {
            $expertise[$result['geoname_id']]['cuisines'][$result['cuisine_id']]['count']++;
            $expertise[$result['geoname_id']]['cuisines'][$result['cuisine_id']]['score']+=$result['score'];
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
     * @param $userId
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getArrayByUserId($userId)
    {
        $sql = "SELECT reviews.id AS id, reviews.restaurant_id AS restaurant_id
                FROM reviews WHERE reviews.user_id = ?";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param $userId
     * @return array
     */
    public function getGeoScoreArrayByUserId($userId)
    {
        $sql = "SELECT
                    COUNT(reviews.id) AS reviews_count,
                    (SUM(reviews.score) + COUNT(reviews.id)) AS score,
                    reviews.geoname_id AS geoname_id
                FROM reviews
                WHERE reviews.user_id = ?
                GROUP BY reviews.geoname_id";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param Review $review
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function decrementReviewsWithHigherPosition(Review $review)
    {
        $cuisine = $review->getCuisine();
        $geoname = $review->getGeoname();
        $user = $review->getUser();

        $sql = 'UPDATE reviews
                SET position = position - 1
                WHERE user_id = ?
                  AND geoname_id = ?
                  AND cuisine_id = ?
                  AND position > ?';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $user->getId());
        $stmt->bindValue(2, $geoname->getId());
        $stmt->bindValue(3, $cuisine->getId());
        $stmt->bindValue(4, $review->getPosition());
        $stmt->execute();
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllRankingsData()
    {
        $sql =
            'SELECT DISTINCT
            user_id AS user,
            geoname_id AS geoname,
            cuisine_id AS cuisine
            FROM reviews';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param int $userId
     * @param int $geonameId
     * @param int $cuisineId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getSingleRankingPositionData($userId, $geonameId, $cuisineId)
    {
        $sql =
            'SELECT id, position
            FROM reviews
            WHERE user_id = ?
            AND geoname_id = ?
            AND cuisine_id = ?
            ORDER BY position ASC';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->bindValue(2, $geonameId);
        $stmt->bindValue(3, $cuisineId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param int $reviewId
     * @param int $position
     */
    public function updatePosition($reviewId, $position)
    {
        $sql = 'UPDATE reviews
                SET position = ?
                WHERE id = ?';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $position);
        $stmt->bindValue(2, $reviewId);
        $stmt->execute();
    }


}