<?php

namespace Tastd\Bundle\CoreBundle\Notification;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\Translator;
use Tastd\Bundle\CoreBundle\Entity\Notification;
use Tastd\Bundle\CoreBundle\Entity\PushMessage;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;

/**
 * Interface NewFollowerNotificationFactory
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
class NewFollowerNotificationFactory implements NotificationFactory
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var Translator */
    protected $translator;

    /**
     * @param EntityManager $entityManager
     * @param Translator    $translator
     */
    public function __construct(EntityManager $entityManager, Translator $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param array $data
     *
     * @return Notification
     */
    public function create($data)
    {
        $leaderId = $data['leader'];
        $notification = New Notification();
        $notification->setName(Notification::NEW_FOLLOWER);
        $notification->setContent(array(
            'follower' => $data['follower'],
            'followerFullName' => $data['followerFullName'],
            'image' => $data['followerImage']
        ));
        $translatedMessage = $this->translator->trans($notification->getTranslationKey(), array(
            '%name%' => $data['followerFullName']
        ));
        $notification->setMessage($translatedMessage);
        $pushMessage = new PushMessage();
        $pushMessage->setNotification($notification);
        $pushMessage->setUser($this->entityManager->getReference(User::CLASS_NAME, $leaderId));
        $notification->addPushMessage($pushMessage);

        return $notification;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return ApiEvent::CONNECTION_CREATED;
    }

}