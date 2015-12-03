<?php

namespace Tastd\Bundle\CoreBundle\Manager;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use PDO;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class ScoreManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class ScoreManager
{
    protected $entityManager;
    /** @var Connection $connection */
    protected $connection;
    /** @var UserRepository  */
    protected $userRepository;

    const COMMON_REVIEW_MULTIPLIER = 5;
    const COUNT_FOLLOWERS_MULTIPLIER = 2;
    const COUNT_RESTAURANT_MULTIPLIER = 1;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
        $this->userRepository = $this->entityManager->getRepository(User::CLASS_NAME);
    }

    /**
     * @param int $userId
     *
     * @return int
     */
    public function calculateRestaurantCountScore($userId)
    {
        $sql =
            "SELECT COUNT(reviews.id) as total
            FROM reviews
            WHERE reviews.user_id = ?";
        $reviewsCount = $this->connection->fetchArray($sql, array($userId), array(\PDO::PARAM_INT));

        $sql =
            "SELECT COUNT(wishes.id) as total
            FROM wishes
            WHERE wishes.user_id = ?";
        $wishesCount = $this->connection->fetchArray($sql, array($userId), array(\PDO::PARAM_INT));

        return ($reviewsCount[0] + $wishesCount[0]) * self::COUNT_RESTAURANT_MULTIPLIER;
    }

    /**
     * @param int   $restaurantId
     * @param array $followerIds
     */
    public function calculateReviewScore($restaurantId, $followerIds)
    {
        $sql =
            "SELECT COUNT(reviews.id) as total
            FROM reviews
            WHERE reviews.user_id IN (?)
            AND reviews.restaurant_id = ?";

        $result = $this->connection->fetchArray(
            $sql,
            array($followerIds, $restaurantId),
            array(Connection::PARAM_INT_ARRAY, \PDO::PARAM_INT));

        return $result[0] * self::COMMON_REVIEW_MULTIPLIER;
    }

    /**
     * @param $userId
     * @return integer
     * @throws \Doctrine\DBAL\DBALException
     */
    public function calculateReviewsUserTotalScore($userId)
    {
        $sql =
            "SELECT SUM(reviews.score)
            FROM reviews
            WHERE reviews.user_id = ?";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_COLUMN);
        if (!$result) {
            $result = 0;
        }

        return $result;
    }


    /**
     * @param $reviewId
     * @param $score
     */
    public function updateReviewScore($reviewId, $score)
    {
        $sql =
            'UPDATE reviews
            SET reviews.score = ?
            WHERE reviews.id = ?';

        $this->connection->executeQuery(
            $sql,
            array($score, $reviewId),
            array(\PDO::PARAM_INT, \PDO::PARAM_INT));
    }

    /**
     * @param $userId
     * @param $geonameId
     * @param $score
     * @param $count
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insertGeoScore($userId, $geonameId, $score, $count)
    {
        $sql =
            'INSERT INTO geo_scores (geoname_id, user_id, score, count)
            VALUES (?, ?, ?, ?)';

        $this->connection->executeQuery(
            $sql,
            array($geonameId, $userId, $score, $count),
            array(\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT)
        );
    }

    /**
     * @param $userId
     * @param $geonameId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteGeoScore($userId, $geonameId)
    {
        $sql = 'DELETE FROM geo_scores WHERE geoname_id = ? AND user_id = ?';
        $this->connection->executeQuery(
            $sql,
            array($geonameId, $userId),
            array(\PDO::PARAM_INT, \PDO::PARAM_INT)
        );
    }

    /**
     * @param $userId
     * @param $score
     */
    public function updateUserScore($userId, $score)
    {
        $sql =
            'UPDATE users
            SET users.score = ?
            WHERE users.id = ?';

        $this->connection->executeQuery(
            $sql,
            array($score, $userId),
            array(\PDO::PARAM_INT, \PDO::PARAM_INT));
    }

    /**
     * @param $userId
     *
     * @return int
     */
    public function getScorePosition($userId)
    {
        $sql =
            'SELECT rank
            FROM (
                SELECT @rownum := @rownum + 1 AS rank, id
                FROM users, (SELECT @rownum := 0) r
                ORDER BY score DESC
                ) selection
            WHERE id = ?';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_COLUMN);

        return (int) $result;

    }

    /**
     * @param $userId
     *
     * @return int
     */
    public function getLastWeekRestaurantsCount($userId)
    {
        $sql =
            'SELECT COUNT(id) FROM reviews
            WHERE user_id = ?
            AND created > DATE_SUB(NOW(), INTERVAL 1 WEEK)';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        $reviewsCount = (int) $stmt->fetch(PDO::FETCH_COLUMN);

        $sql =
            'SELECT COUNT(id) FROM wishes
            WHERE user_id = ?
            AND created > DATE_SUB(NOW(), INTERVAL 1 WEEK)';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        $wishesCount = (int) $stmt->fetch(PDO::FETCH_COLUMN);


        return $reviewsCount + $wishesCount;
    }

    /**
     * @param $userId
     *
     * @return int
     */
    public function getLastWeekFollowersCount($userId)
    {
        $sql =
            'SELECT COUNT(id) FROM connections
            WHERE leader_id = ?
            AND created > DATE_SUB(NOW(), INTERVAL 1 WEEK)';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * All my reviews in common with my followers' reviews of the last week
     * @param $userId
     *
     * @return int
     */
    public function getLastWeekCommonReviewsCount($userId)
    {
        $followerIds = $this->userRepository->getFollowersIds($userId);
        $sql =
            'SELECT COUNT(*) as counter
            FROM (
                SELECT reviews.id, reviews.restaurant_id
                FROM reviews
                WHERE reviews.user_id = ?) my_reviews
            INNER JOIN (
                SELECT reviews.id, reviews.restaurant_id
                FROM reviews
                WHERE reviews.user_id IN (?)
                AND reviews.created > DATE_SUB(NOW(), INTERVAL 1 WEEK)) follower_reviews
            ON my_reviews.restaurant_id = follower_reviews.restaurant_id';

        $result = $this->connection->fetchArray(
            $sql,
            array($userId, $followerIds),
            array(\PDO::PARAM_INT, Connection::PARAM_INT_ARRAY));

        return (int) $result[0];

    }

}