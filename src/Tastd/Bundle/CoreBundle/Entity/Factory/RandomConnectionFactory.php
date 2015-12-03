<?php

namespace Tastd\Bundle\CoreBundle\Entity\Factory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class RandomConnectionFactory
 *
 * @package Tastd\Bundle\CoreBundle\Entity\Factory
 */
class RandomConnectionFactory
{

    protected $entityManager;
    /** @var Connection  */
    protected $connection;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $this->entityManager->getConnection();
    }

    /**
     * @param $users
     */
    public function createAll($users)
    {
        // Every user follow all previous
        $leaders = array();
        foreach ($users as $user) {
            $this->follow($user, $leaders);
            $this->entityManager->flush();
            $leaders[] = $user->getId();
        }
    }

    /**
     * @param User $follower
     * @param $leaders
     */
    protected function follow($follower, $leaders)
    {
        $counter = 0;
        $max = rand(1, 100);
        shuffle($leaders);
        /** @var User $leader */
        foreach ($leaders as $leader) {
            $this->connection->executeQuery('INSERT INTO connections (leader_id, follower_id, status) VALUES ('. $leader.',' . $follower->getId() . ', "APPROVED")');
            $counter++;
            if ($counter > $max) {
                return;
            }
        }
    }

}