<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Tastd\Bundle\CoreBundle\Cache\CacheManager;

/**
 * Class CacheListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class CacheListener implements EventSubscriberInterface
{

    protected $cacheManager;

    /**
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
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
        // $object = $event->getObject();
        // $this->cacheManager->tagObject($object);
    }
}