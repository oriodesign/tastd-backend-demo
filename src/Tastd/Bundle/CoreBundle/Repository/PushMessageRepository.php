<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\View\View;
use Pagerfanta\Pagerfanta;
use PDO;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\PushMessage;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\PushMessageNotFoundException;

/**
 * Class PushMessageRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class PushMessageRepository extends BaseEntityRepository
{

    /**
     * @param integer $id
     * @return PushMessage
     * @throws PushMessageNotFoundException
     */
    public function get($id)
    {
        $pushMessage = $this->find($id);
        if (!$pushMessage) {
            throw new PushMessageNotFoundException();
        }

        return $pushMessage;
    }

    /**
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getAll(Request $request)
    {
        $userId = $request->query->get('user');
        $pushMessagesParameters = $request->request->get('pushMessages');

        $page = $request->query->get('page', 1);

        $queryBuilder = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('p, n')
            ->from(PushMessage::SHORTCUT_CLASS_NAME, 'p')
            ->leftJoin('p.notification', 'n');

        if (isset($userId)) {
            $queryBuilder
                ->leftJoin('p.user', 'u')
                ->andWhere('p.user = :userId')
                ->setParameter('userId', $userId);
        }

        if (isset($pushMessagesParameters)) {
            $ids = explode(',', $pushMessagesParameters);
            $queryBuilder
                ->where('p.id IN (:ids)')
                ->setParameter('ids', $ids);
        }

        $queryBuilder->orderBy('n.created', 'DESC');

        return $this->getPager($queryBuilder, $page);
    }

    /**
     * @param int $userId
     *
     * @return int
     */
    public function getUnseenCounter($userId)
    {
        $sql = '
            SELECT COUNT(push_messages.id)
            FROM push_messages
            WHERE push_messages.seen = 0
            AND push_messages.user_id = ?';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindParam(1, $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRecentlyPushedUsersIds()
    {
        $sql = '
          SELECT DISTINCT push_messages.user_id
          FROM push_messages
          LEFT JOIN notifications ON push_messages.notification_id = notifications.id
          WHERE notifications.created > DATE_SUB(NOW(), INTERVAL 6 HOUR)
          ';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $results;
    }


}