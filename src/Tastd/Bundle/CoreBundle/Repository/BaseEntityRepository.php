<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Tastd\Bundle\CoreBundle\Pager\OutOfRangePager;

/**
 * Class ConnectionRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class BaseEntityRepository extends EntityRepository
{

    protected $leadersBag;
    protected $followersBag;

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager         $em    The EntityManager to use.
     * @param ClassMetadata $class The class descriptor.
     */
    public function __construct($em, ClassMetadata $class)
    {
        $this->_entityName = $class->name;
        $this->_em         = $em;
        $this->_class      = $class;
        $this->leadersBag = array();
        $this->followersBag = array();
    }

    /**
     * @param $userId
     *
     * @return array
     */
    public function getLeadersOf($userId)
    {
        if (isset($this->leadersBag[$userId])) {
            return $this->leadersBag[$userId];
        }

        $sql = 'SELECT connections.leader_id AS id FROM connections WHERE connections.follower_id = :follower_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('follower_id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $leadersId = array_reduce($results, function($carry, $item){
            $carry[] = $item['id'];
            return $carry;
        }, array());

        $this->leadersBag[$userId] = $leadersId;

        return $leadersId;
    }

    public function getFollowersOf($userId)
    {
        if (isset($this->followersBag[$userId])) {
            return $this->followersBag[$userId];
        }

        $sql = 'SELECT connections.follower_id AS id FROM connections WHERE connections.leader_id = :leader_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('leader_id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $followersId = array_reduce($results, function($carry, $item){
            $carry[] = $item['id'];
            return $carry;
        }, array());

        $this->followersBag[$userId] = $followersId;

        return $followersId;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param integer      $pageNumber
     *
     * @return Pagerfanta
     */
    protected function getPager($queryBuilder, $pageNumber = 1)
    {
        $adapter = new DoctrineORMAdapter($queryBuilder);
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


}