<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Tastd\Bundle\CoreBundle\Entity\Tag;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Repository\TagGroupRepository;

/**
 * Class TagSerializeListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class TagSerializeListener implements EventSubscriberInterface
{

    protected $tagGroupRepository;
    protected $serializedEntities;

    /**
     * @param TagGroupRepository $tagGroupRepository
     */
    public function __construct(TagGroupRepository $tagGroupRepository)
    {
        $this->tagGroupRepository = $tagGroupRepository;
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
        /** @var Tag $tag */
        $tag = $event->getObject();

        if (!$tag instanceof Tag) {
            return;
        }

        if (in_array($tag->getId(), $this->serializedEntities)) {
            return;
        }

        $this->serializedEntities[] = $tag->getId();

        $tag->setGroupName($this->tagGroupRepository->getGroupNameById($tag->getGroupId()));
    }
}