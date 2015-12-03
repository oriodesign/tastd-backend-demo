<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use PDO;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Device;
use Tastd\Bundle\CoreBundle\Entity\Notification;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\DeviceNotFoundException;

/**
 * Class DeviceRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class DeviceRepository extends EntityRepository
{
    /**
     * @param integer $id
     *
     * @return Device
     * @throws DeviceNotFoundException
     */
    public function get($id)
    {
        $device = $this->find($id);
        if (!$device) {
            throw new DeviceNotFoundException();
        }

        return $device;
    }

    /**
     * @param string $token
     * @return Device|null
     */
    public function getDeviceByToken($token)
    {
        try {
            $device = $this
                ->getEntityManager()
                ->createQueryBuilder()
                ->select('d')
                ->from(Device::SHORTCUT_CLASS_NAME, 'd')
                ->where('d.token = :token')
                ->setParameter('token', $token)
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }

        return $device;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAll(Request $request)
    {
        $user = $request->query->get('user');

        $queryBuilder = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('d')
            ->from(Device::SHORTCUT_CLASS_NAME, 'd');

        if ($user) {
            $queryBuilder
                ->andWhere('d.user = :user')
                ->setParameter('user', $user);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Notification $notification
     *
     * @return array
     */
    public function getNotificationTokens(Notification $notification)
    {
        $sql =
           'SELECT recipients.badge AS badge, devices.token as token
            FROM (
                SELECT COUNT(push_messages.id) AS badge, push_messages.user_id AS user_id
                FROM push_messages
                WHERE push_messages.seen = 0
                GROUP BY push_messages.user_id
                ) AS recipients
            LEFT JOIN push_messages ON push_messages.user_id = recipients.user_id
            LEFT JOIN devices ON devices.user_id = recipients.user_id
            WHERE push_messages.notification_id = :notification_id
            AND  devices.token IS NOT NULL; ';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('notification_id', $notification->getId());
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }

    /**
     * @param $userId
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getFollowersTokens($userId)
    {
        $sql = 'SELECT devices.token AS token
              FROM devices
              LEFT JOIN connections ON devices.user_id = connections.follower_id
              WHERE connections.leader_id = :leader_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('leader_id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $results;
    }

    /**
     * @param $userId
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUserTokens($userId)
    {
        $sql = 'SELECT devices.token AS token
              FROM devices
              WHERE devices.user_id = :user_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $results;
    }

    /**
     * @param $facebookIds
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getFacebookFriendsTokens($facebookIds)
    {
        $sql = 'SELECT devices.token AS token
              FROM devices
              LEFT JOIN credentials
              ON credentials.user_id = devices.user_id
              WHERE credentials.external_id IN (?)';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue(1, $facebookIds, Connection::PARAM_INT_ARRAY);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $results;
    }
}