<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContext;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Key\SerializationGroup;
use Tastd\Bundle\CoreBundle\Manager\UserMetaManager;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class UserSerializeListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class UserSerializeListener implements EventSubscriberInterface
{

    protected $cloudfrontUrl;
    protected $userMetaManager;
    protected $requestStack;
    protected $serializedEntities;

    /**
     * @param $cloudfrontUrl
     * @param UserMetaManager $userMetaManager
     * @param RequestStack    $requestStack
     */
    public function __construct(
        $cloudfrontUrl,
        UserMetaManager $userMetaManager,
        RequestStack $requestStack)
    {
        $this->cloudfrontUrl = $cloudfrontUrl;
        $this->userMetaManager = $userMetaManager;
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
        /** @var User $user */
        $user = $event->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (in_array($user->getId(), $this->serializedEntities)) {
            return;
        }

        $this->serializedEntities[] = $user->getId();
        $this->hydrateAvatar($user);
        $this->hydrateWithSerializationGroups($user);
    }

    /**
     * @param User $user
     */
    private function hydrateWithSerializationGroups(User $user)
    {
        $request = $this->requestStack->getCurrentRequest();
        $serializationGroupsParam = $request->query->get('serializationGroups');
        $serializationGroups = explode(',', $serializationGroupsParam);
        if (in_array('isMyFollower', $serializationGroups)) {
            $this->userMetaManager->hydrateIsMyFollower($user);
        }

        if (in_array('isMyLeader', $serializationGroups)) {
            $this->userMetaManager->hydrateIsMyLeader($user);
        }
    }

    /**
     * @param User $user
     */
    private function hydrateAvatar(User $user)
    {
        if ($user->getAvatar()) {
            $user->setAvatar($this->cloudfrontUrl . $user->getAvatar());
        } else {
            $user->setAvatar($this->cloudfrontUrl . 'avatar/_default_avatar.png');
        }
    }
}