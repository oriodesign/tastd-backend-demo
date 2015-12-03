<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Tastd\Bundle\CoreBundle\Entity\Flag;
use Tastd\Bundle\CoreBundle\Manager\FlagMetaManager;

/**
 * Class FlagSerializeListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class FlagSerializeListener implements EventSubscriberInterface
{

    protected $cloudfrontUrl;
    protected $requestStack;
    protected $flagMetaManager;

    /**
     * @param string          $cloudfrontUrl
     * @param RequestStack    $requestStack
     */
    public function __construct($cloudfrontUrl, RequestStack $requestStack)
    {
        $this->cloudfrontUrl = $cloudfrontUrl;
        $this->requestStack = $requestStack;
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
        /** @var Flag $user */
        $flag = $event->getObject();
        if (!$flag instanceof Flag) {
            return;
        }

        $this->hydratePictureAndThumb($flag);
    }

    /**
     * @param Flag $flag
     */
    protected function hydratePictureAndThumb(Flag $flag)
    {
        if ($flag->getPicture()) {
            $flag->setPicture($this->cloudfrontUrl . $flag->getPicture());
        } else {
            $flag->setPicture($this->cloudfrontUrl . 'restaurant_thumb/_default_restaurant.png');
        }
    }
}