<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Tastd\Bundle\CoreBundle\Entity\Notification;

/**
 * Class NotificationSerializeListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class NotificationSerializeListener implements EventSubscriberInterface
{

    protected $cloudfrontUrl;
    protected $serializedEntities;

    /**
     * @param string                $cloudfrontUrl
     */
    public function __construct(
        $cloudfrontUrl)
    {
        $this->cloudfrontUrl = $cloudfrontUrl;
        $this->serializedEntities = array();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }

    /**
     * @param PreSerializeEvent $event
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        /** @var Notification $user */
        $notification = $event->getObject();
        if (!$notification instanceof Notification) {
            return;
        }

        if (in_array($notification->getId(), $this->serializedEntities)) {
            return;
        }

        $this->serializedEntities[] = $notification->getId();
        $this->hydratePicture($notification);
    }

    /**
     * @param Notification $notification
     */
    protected function hydratePicture(Notification $notification)
    {
        $content = $notification->getContent();

        if (array_key_exists('image', $content)) {
            $content['image'] = $this->cloudfrontUrl . $content['image'];
        } else {
            $content['image'] = $this->cloudfrontUrl . 'avatar/_default_avatar.png';
        }

        $notification->setContent($content);
    }

}