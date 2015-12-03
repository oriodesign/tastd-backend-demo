<?php

namespace Tastd\Bundle\CoreBundle\Notification;

use RMS\PushNotificationsBundle\Message\iOSMessage;
use RMS\PushNotificationsBundle\Service\Notifications;
use Symfony\Component\Translation\Translator;
use Tastd\Bundle\CoreBundle\Entity\Notification;
use Tastd\Bundle\CoreBundle\Repository\DeviceRepository;

/**
 * Class NotificationDispatcher
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
class NotificationDispatcher
{
    /** @var Notifications */
    protected $notificationsService;
    /** @var DeviceRepository */
    protected $deviceRepository;

    /**
     * @param Notifications    $notificationsService
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(
        Notifications $notificationsService,
        DeviceRepository $deviceRepository
    )
    {
        $this->notificationsService = $notificationsService;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param Notification $notification
     */
    public function dispatchPushMessages(Notification $notification)
    {
        $deviceTokens = $this->deviceRepository->getNotificationTokens($notification);
        foreach ($deviceTokens as $deviceToken) {
            $pushNotification = $this->createMessage($notification, $deviceToken);
            $this->notificationsService->send($pushNotification);
        }
    }

    /**
     * @param Notification $notification
     * @param string       $deviceToken
     *
     * @return iOSMessage
     */
    protected function createMessage(Notification $notification, $deviceToken)
    {

        $pushNotification = new iOSMessage();
        $pushNotification->setMessage($notification->getMessage());
        $pushNotification->setDeviceIdentifier($deviceToken['token']);
        $pushNotification->setAPSSound('default');
        $pushNotification->setAPSBadge($deviceToken['badge']);

        /* @TODO send extra data to the client or not
        foreach ($notification->getContent() as $key => $value) {
            $pushNotification->addCustomData($key, $value);
        }
        */

        return $pushNotification;
    }
}