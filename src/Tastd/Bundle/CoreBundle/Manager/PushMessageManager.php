<?php

namespace Tastd\Bundle\CoreBundle\Manager;

use Doctrine\ORM\EntityManager;
use PDO;

/**
 * Class PushMessageManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class PushMessageManager
{
    protected $entityManager;
    protected $connection;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }

    public function markAllAsSeen($userId)
    {
        $sql =
            'UPDATE push_messages
            SET push_messages.seen = 1
            WHERE push_messages.user_id = ?';

        $this->connection->executeQuery(
            $sql,
            array($userId),
            array(\PDO::PARAM_INT));

    }


}
