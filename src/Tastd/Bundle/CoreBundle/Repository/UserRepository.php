<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use PDO;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Connection;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\UserNotFoundException;
use Tastd\Bundle\CoreBundle\Key\ConnectionStatus;
use Tastd\Bundle\CoreBundle\Pager\OutOfRangePager;

/**
 * Class UserRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class UserRepository extends BaseEntityRepository
{

    /**
     * @param integer $id
     *
     * @return null|User
     * @throws UserNotFoundException
     */
    public function get($id)
    {
        $user = $this->find($id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @param string $ids
     * @return array
     */
    public function getByIds($ids)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', explode(',', $ids));

        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * @return integer
     */
    public function countAll()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(u)')
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getUsersPager($request)
    {
        $fullName = $request->query->get('query');
        $pageNumber = $request->query->get('page', 1);
        $leadersOf = $request->query->get('leadersOf');
        $followersOf = $request->query->get('followersOf');
        $notUsers = $request->query->get('notUsers');
        $orderBy = $request->query->get('orderBy');
        $geoname = $request->query->get('geoname');
        $featured = $request->query->get('featured');
        $emails = $request->query->get('emails');

        if ($orderBy === 'geoScore' && $geoname && $geoname !== 'is_null') {
            $select = 'u,g,gs,gg';
        } else {
            $select = 'u,g';
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select($select)
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->leftJoin('u.geoname', 'g');

        if ($fullName) {
            $queryBuilder
                ->andWhere('u.fullName LIKE :fullName')
                ->setParameter('fullName', '%'.$fullName.'%');
        }

        if ($geoname && $geoname !== 'is_null') {
            $queryBuilder
                ->leftJoin('u.reviews', 'r')
                ->andWhere('r.geoname = :geoname')
                ->setParameter('geoname', $geoname);
        } else if ($geoname && $geoname === 'is_null') {
            $queryBuilder
                ->andWhere('u.geoname IS NULL');
        }

        if ($followersOf) {
            $followerIds = $this->getFollowersIds($followersOf);
            if (count($followerIds) > 0) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('u.id', $followerIds));
            }
        }

        if ($leadersOf) {
            $leaderIds = $this->getLeadersIds($leadersOf);
            if (count($leaderIds) > 0) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('u.id', $leaderIds));
            }
        }

        if ($notUsers) {
            $notUsersIds = explode(',',$notUsers);
            if (count($notUsersIds) > 0) {
                $queryBuilder->andWhere($queryBuilder->expr()->notIn('u.id', $notUsersIds));
            }
        }

        if ($emails) {
            $queryBuilder
                ->andWhere(('MD5(u.email) IN (:emails)'))
                ->setParameter('emails', explode(',', $emails));
        }

        if ($featured) {
            $featured = $featured === 'true' ? 1 : 0;
            $queryBuilder
                ->andWhere('u.featured = :featured')
                ->setParameter('featured', $featured);
        }

        if ($orderBy === 'fullName') {
            $queryBuilder
                ->orderBy('u.fullName', 'ASC');
        }

        if ($orderBy === 'score') {
            $queryBuilder
                ->orderBy('u.score', 'DESC');
        }

        if ($orderBy === 'geoScore' && $geoname && $geoname !== 'is_null') {
            $queryBuilder
                ->leftJoin('u.geoScores', 'gs')
                ->leftJoin('gs.geoname', 'gg')
                ->andWhere('gs.geoname = :geoname')
                ->orderBy('gs.score', 'DESC');
        }

        return $this->getPager($queryBuilder, $pageNumber);
    }


    /**
     * @param int $user
     *
     * @return array
     */
    public function getLeadersIds($user)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('l.id')
            ->from(User::SHORTCUT_CLASS_NAME, 'l')
            ->leftJoin('l.followers', 'c')
            ->leftJoin('c.follower', 'u')
            ->where('u = :user')
            ->andWhere('c.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', ConnectionStatus::APPROVED);

        $res = $queryBuilder->getQuery()->getArrayResult();
        $ids = array_map(function($data){
            return $data['id'];
        }, $res);

        return $ids;
    }

    /**
     * @param $userId
     * @return array
     *
     * @throws DBALException
     */
    public function getFollowersIds($userId)
    {
        $sql = "SELECT connections.follower_id FROM connections WHERE connections.leader_id = ?";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param int $userId
     * @param int $wishId
     *
     * @return array
     *
     * @throws DBALException
     */
    public function getFollowersIdsWithWish($userId, $wishId)
    {
        $sql = "
          SELECT DISTINCT connections.follower_id
          FROM connections
          LEFT JOIN wishes ON wishes.user_id = connections.follower_id
          WHERE connections.leader_id = ?
          AND wishes.id = ?";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->bindValue(2, $wishId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * @param string $provider
     * @param string $token
     *
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getSingleUserByCredentialToken($provider, $token)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQueryBuilder()
            ->select('u, c')
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->leftJoin('u.credentials', 'c')
            ->where('c.provider = :provider')
            ->andWhere('c.token = :token')
            ->setParameter('provider', $provider)
            ->setParameter('token', $token);

        $query = $queryBuilder->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function getUsersByEmailWithCredential($email)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQueryBuilder()
            ->select('u, c')
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->leftJoin('u.credentials', 'c')
            ->where('u.email = :email')
            ->setParameter('email', $email);
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $provider
     * @param array  $ids
     *
     * @return array
     */
    public function getUsersByCredentialExternalIds($provider, array $ids)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQueryBuilder()
            ->select('u, c')
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->leftJoin('u.credentials', 'c')
            ->where('c.provider = :provider')
            ->andWhere('c.externalId IN (:ids)')
            ->setParameter('provider', $provider)
            ->setParameter('ids', $ids)
            ->orderBy('u.lastName', 'ASC');
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $provider
     * @param string $id
     *
     * @return mixed
     */
    public function getUserByCredentialExternalId($provider, $id)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQueryBuilder()
            ->select('u, c')
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->leftJoin('u.credentials', 'c')
            ->where('c.provider = :provider')
            ->andWhere('c.externalId = :id')
            ->setParameter('provider', $provider)
            ->setParameter('id', $id);
        $query = $queryBuilder->getQuery();

        return $query->getSingleResult();
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
            ->select('DATE(u.created) as day, COUNT(u) as counter')
            ->groupBy('day')
            ->from(User::SHORTCUT_CLASS_NAME, 'u')
            ->where('u.created > :from')
            ->andWhere('u.created < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param string $name
     * @param User   $user
     * @param int    $pageNumber
     *
     * @return Pagerfanta
     */
    public function getUsersWithCommonLeaders($name, User $user, $pageNumber = 1)
    {
        $sqlAllUsers =
            "SELECT
                users.id as user_id,
                users.avatar as avatar,
                users.full_name as full_name,
                0 as common_leaders_count
            FROM users
            WHERE users.id <> :user_id
            AND full_name LIKE :name";

        $sqlCommonLeaders =
            "SELECT
                c2.follower_id as user_id,
                '' as full_name,
                '' as avatar,
                COUNT(c1.leader_id) as common_leaders_count
            FROM connections c1
            INNER JOIN connections c2
                ON c1.leader_id = c2.leader_id
                AND c1.follower_id <> c2.follower_id
            WHERE c1.follower_id = :user_id
            AND c1.status = :connection_status
            GROUP BY c1.follower_id, c2.follower_id";

        $sql =
            "SELECT
                results.user_id as id,
                results.avatar as avatar,
                results.full_name as full_name,
                SUM(results.common_leaders_count) as common_leaders_count
            FROM (%s UNION ALL %s) AS results
            GROUP BY id
            HAVING full_name LIKE :name
            ORDER BY common_leaders_count DESC";

        $sql = sprintf($sql, $sqlAllUsers, $sqlCommonLeaders);
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('connection_status', ConnectionStatus::APPROVED);
        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('name', '%'.$name.'%');
        $stmt->execute();
        $results = $stmt->fetchAll();
        $adapter = new ArrayAdapter($results);
        $pager = new Pagerfanta($adapter);
        try {
            $pager = $pager->setCurrentPage($pageNumber);
        } catch (\Exception $e) {
            $adapter = new ArrayAdapter(array());
            $pager = new OutOfRangePager($adapter);
            $pager->setOriginalPage($pageNumber);
        }

        return $pager;
    }

    /**
     * @param string $name
     * @param User   $user
     * @param int    $pageNumber
     *
     * @return Pagerfanta
     */
    public function getUsersWithCommonFollowers($name, User $user, $pageNumber = 1)
    {
        $sqlAllUsers =
            "SELECT
                users.id as user_id,
                users.full_name as full_name,
                users.avatar as avatar,
                0 as common_followers_count
            FROM users
            WHERE users.id <> :user_id
            AND full_name LIKE :name";

        $sqlCommonLeaders =
            "SELECT
                c2.leader_id as user_id,
                '' as full_name,
                '' as avatar,
                COUNT(c1.follower_id) as common_followers_count
            FROM connections c1
            INNER JOIN connections c2
                ON c1.follower_id = c2.follower_id
                AND c1.leader_id <> c2.leader_id
            WHERE c1.leader_id = :user_id
            AND c1.status = :connection_status
            GROUP BY c1.leader_id, c2.leader_id";

        $sql =
            "SELECT
                results.user_id as id,
                results.full_name as full_name,
                results.avatar as avatar,
                SUM(results.common_followers_count) as common_followers_count
            FROM (%s UNION ALL %s) AS results
            GROUP BY id
            HAVING full_name LIKE :name
            ORDER BY common_followers_count DESC";

        $sql = sprintf($sql, $sqlAllUsers, $sqlCommonLeaders);
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('connection_status', ConnectionStatus::APPROVED);
        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('name', '%'.$name.'%');
        $stmt->execute();
        $results = $stmt->fetchAll();
        $adapter = new ArrayAdapter($results);
        $pager = new Pagerfanta($adapter);
        try {
            $pager = $pager->setCurrentPage($pageNumber);
        } catch (\Exception $e) {
            $adapter = new ArrayAdapter(array());
            $pager = new OutOfRangePager($adapter);
            $pager->setOriginalPage($pageNumber);
        }

        return $pager;
    }

    /**
     * @return array
     *
     * @throws DBALException
     */
    public function getAllIds()
    {
        $sql = "SELECT users.id FROM users";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


}