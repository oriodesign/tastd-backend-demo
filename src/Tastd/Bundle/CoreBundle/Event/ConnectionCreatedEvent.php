<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Connection;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class ConnectionCreatedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class ConnectionCreatedEvent extends ApiEvent
{
    /** @var Connection  */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->connection->getFollower();
    }

}