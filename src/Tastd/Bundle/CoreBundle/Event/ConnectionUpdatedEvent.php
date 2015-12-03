<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Connection;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class ConnectionDeletedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class ConnectionUpdatedEvent extends ApiEvent
{
    /** @var Connection */
    protected $newConnection;

    /** @var Connection */
    protected $oldConnection;

    /**
     * @param Connection $oldConnection
     * @param Connection $newConnection
     */
    public function __construct(Connection $oldConnection, Connection $newConnection)
    {
        $this->newConnection = $newConnection;
        $this->oldConnection = $oldConnection;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\Connection $newConnection
     */
    public function setNewConnection($newConnection)
    {
        $this->newConnection = $newConnection;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Connection
     */
    public function getNewConnection()
    {
        return $this->newConnection;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\Connection $oldConnection
     */
    public function setOldConnection($oldConnection)
    {
        $this->oldConnection = $oldConnection;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Connection
     */
    public function getOldConnection()
    {
        return $this->oldConnection;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->oldConnection->getFollower();
    }


}