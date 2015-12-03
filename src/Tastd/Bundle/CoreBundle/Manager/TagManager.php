<?php

namespace Tastd\Bundle\CoreBundle\Manager;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;

/**
 * Class TagManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class TagManager
{
    /** @var EntityManager $entityManager */
    protected $entityManager;
    /** @var Connection $connection */
    protected $connection;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateCount()
    {
        $sql =
            'UPDATE tags
            SET tags.count = (
                SELECT COUNT(review_tag.tag_id)
                FROM review_tag
                WHERE review_tag.tag_id = tags.id
                GROUP BY review_tag.tag_id
            )';

        $this->connection->executeQuery($sql);
    }

}