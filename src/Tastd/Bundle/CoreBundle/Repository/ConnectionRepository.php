<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Tastd\Bundle\CoreBundle\Entity\Connection;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\AddressNotFoundException;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\ConnectionNotFoundException;

/**
 * Class ConnectionRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class ConnectionRepository extends BaseEntityRepository
{
    /**
     * @param integer $id
     *
     * @return Connection
     * @throws ConnectionNotFoundException
     */
    public function get($id)
    {
        $connection = $this->find($id);
        if (!$connection) {
            throw new ConnectionNotFoundException();
        }

        return $connection;
    }

    /**
     * @return integer
     */
    public function countAll()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(c)')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->getQuery()
            ->getSingleScalarResult();
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
            ->select('DATE(c.created) as day, COUNT(c) as counter')
            ->groupBy('day')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->where('c.created > :from')
            ->andWhere('c.created < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getTopLeaders()
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('l.id, l.fullName, COUNT(c) as followerCount')
            ->groupBy('l.id')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->leftJoin('c.leader', 'l')
            ->orderBy('followerCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return array
     */
    public function getTopFollowers()
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('f.id, f.fullName, COUNT(c) as leaderCount')
            ->groupBy('f.id')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->leftJoin('c.follower', 'f')
            ->orderBy('leaderCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User    $user
     * @param string  $status
     * @param integer $pageNumber
     *
     * @return Pagerfanta
     */
    public function getFollowersConnectionsPager(User $user, $status = null, $pageNumber = 1)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c, f')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->where('c.leader = :user')
            ->leftJoin('c.follower', 'f')
            ->setParameter('user', $user);

        if ($status) {
            $queryBuilder
                ->andWhere('c.status = :status')
                ->setParameter('status', $status);
        }

        return $this->getPager($queryBuilder, $pageNumber);
    }

    /**
     * @param User    $user
     * @param string  $status
     * @param string  $fullName
     * @param integer $pageNumber
     * @param integer $geonameId
     * @param string  $orderBy
     *
     * @return Pagerfanta
     */
    public function getLeadersPager(User $user, $status = null, $fullName = null, $pageNumber = 1, $geonameId = null, $orderBy = null)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c, l')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->leftJoin('c.leader', 'l')
            ->where('c.follower = :user')
            ->setParameter('user', $user);

        if ($fullName) {
            $queryBuilder
                ->andWhere('l.fullName LIKE :fullName')
                ->setParameter('fullName', '%'.$fullName.'%');
        }

        if ($geonameId) {
            $queryBuilder
                ->leftJoin('l.reviews', 'r')
                ->andWhere('r.geoname = :geoname')
                ->setParameter('geoname', $geonameId);
        }

        if ($status) {
            $queryBuilder
                ->andWhere('c.status = :status')
                ->setParameter('status', $status);
        }

        if ($orderBy) {
            $queryBuilder
                ->orderBy('l.score', 'DESC');
        }

        return $this->getPager($queryBuilder, $pageNumber);
    }

    /**
     * @param User $follower
     * @param User $leader
     *
     * @return Connection
     *
     * @throws ConnectionNotFoundException
     */
    public function getConnectionByFollowerAndLeader(User $follower, User $leader)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->where('c.leader = :leader')
            ->andWhere('c.follower = :follower')
            ->setParameter('leader', $leader)
            ->setParameter('follower', $follower)
            ->getQuery();

        try {
            return $query->getSingleResult();
        } catch (\Exception $e) {
            throw new ConnectionNotFoundException();
        }
    }

    /**
     * @param User $follower
     *
     * @return array
     */
    public function getConnectionsByFollower(User $follower)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c, l')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->leftJoin('c.leader', 'l')
            ->where('c.follower = :follower')
            ->setParameter('follower', $follower)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $leader
     *
     * @return array
     */
    public function getConnectionsByLeader(User $leader)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c, f')
            ->from(Connection::SHORTCUT_CLASS_NAME, 'c')
            ->leftJoin('c.follower', 'f')
            ->where('c.leader = :leader')
            ->setParameter('leader', $leader)
            ->getQuery();

        return $query->getResult();
    }





}