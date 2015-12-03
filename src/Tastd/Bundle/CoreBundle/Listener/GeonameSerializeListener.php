<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;

/**
 * Class GeonameSerializeListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class GeonameSerializeListener implements EventSubscriberInterface
{

    protected $serializedEntities;

    /**
     * __construct
     */
    public function __construct()
    {
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
        /** @var Geoname $geoname */
        $geoname = $event->getObject();
        if (!$geoname instanceof Geoname) {
            return;
        }

        if (in_array($geoname->getId(), $this->serializedEntities)) {
            return;
        }

        $this->serializedEntities[] = $geoname->getId();

        if ($geoname->getCountry() === 'US') {
            $geoname->setFormattedName($geoname->getAsciiName() . ', ' . $geoname->getAdmin1());
        } else {
            $geoname->setFormattedName($geoname->getAsciiName() . ' (' . $geoname->getCountry() . ')');
        }
    }
}