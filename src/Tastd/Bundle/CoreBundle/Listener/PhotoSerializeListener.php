<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Tastd\Bundle\CoreBundle\Entity\Photo;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class PhotoSerializeListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class PhotoSerializeListener implements EventSubscriberInterface
{

    protected $cloudfrontUrl;
    protected $serializedEntities;

    /**
     * @param string $cloudfrontUrl
     */
    public function __construct($cloudfrontUrl)
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
        /** @var Photo $photo */
        $photo = $event->getObject();
        if (!$photo instanceof Photo) {
            return;
        }

        if (in_array($photo->getId(), $this->serializedEntities)) {
            return;
        }

        $this->serializedEntities[] = $photo->getId();

        $photo->setSrc($this->cloudfrontUrl . $photo->getSrc());
        $photo->setThumb($this->cloudfrontUrl . $photo->getThumb());

    }
}