<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class RestaurantSerializeListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class RestaurantSerializeListener implements EventSubscriberInterface
{

    protected $requestStack;
    protected $cloudfrontUrl;
    protected $serializedEntities;

    /**
     * @param RequestStack          $requestStack
     * @param string                $cloudfrontUrl
     */
    public function __construct(
        RequestStack $requestStack,
        $cloudfrontUrl)
    {
        $this->cloudfrontUrl = $cloudfrontUrl;
        $this->requestStack = $requestStack;
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
        /** @var Restaurant $user */
        $restaurant = $event->getObject();
        if (!$restaurant instanceof Restaurant) {
            return;
        }

        if (in_array($restaurant->getId(), $this->serializedEntities)) {
            return;
        }

        $this->serializedEntities[] = $restaurant->getId();
        $this->hydratePictureAndThumb($restaurant);
    }

    /**
     * @param Restaurant $restaurant
     */
    protected function hydratePictureAndThumb(Restaurant $restaurant)
    {
        if ($restaurant->getPicture()) {
            $restaurant->setPicture($this->cloudfrontUrl . $restaurant->getPicture());
        } else {
            $restaurant->setPicture($this->cloudfrontUrl . 'restaurant/_default_restaurant_cover.png');
        }

        if ($restaurant->getThumb()) {
            $restaurant->setThumb($this->cloudfrontUrl . $restaurant->getThumb());
        } else {
            $restaurant->setThumb($this->cloudfrontUrl . 'restaurant_thumb/_default_restaurant.png');
        }
    }


}