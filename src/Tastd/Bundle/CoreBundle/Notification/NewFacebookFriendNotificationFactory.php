<?php

namespace Tastd\Bundle\CoreBundle\Notification;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\Translator;
use Tastd\Bundle\CoreBundle\Entity\Notification;
use Tastd\Bundle\CoreBundle\Entity\PushMessage;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Facebook\FacebookClient;
use Tastd\Bundle\CoreBundle\Key\CredentialProvider;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * NewFacebookFriendNotificationFactory
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
class NewFacebookFriendNotificationFactory implements NotificationFactory
{
    /** @var UserRepository */
    protected $userRepository;
    /** @var EntityManager */
    protected $entityManager;
    /** @var FacebookClient */
    protected $facebookClient;
    /** @var Translator */
    protected $translator;

    /**
     * @param UserRepository $userRepository
     * @param EntityManager  $entityManager
     * @param FacebookClient $facebookClient
     * @param Translator     $translator
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManager $entityManager,
        FacebookClient $facebookClient,
        Translator $translator)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->facebookClient = $facebookClient;
        $this->translator = $translator;
    }

    /**
     * @param array $data
     *
     * @return Notification
     * @throws \Exception
     */
    public function create($data)
    {
        $userId = $data['user'];
        $user = $this->userRepository->get($userId);
        $credential = $user->getCredentialByProvider(CredentialProvider::FACEBOOK);
        $friends = array();

        if ($credential) {
            $this->facebookClient->connect($credential->getToken());
            $friendsIds = $this->facebookClient->getFriendsIds();
            $friends = $this->userRepository->getUsersByCredentialExternalIds(CredentialProvider::FACEBOOK, $friendsIds);
        }

        $notification = New Notification();
        $notification->setName(Notification::NEW_FACEBOOK_FRIEND);
        $notification->setContent(array(
            'user' => $userId,
            'userFullName' => $data['userFullName'],
            'image' => $data['image']
        ));
        $translatedMessage = $this->translator->trans($notification->getTranslationKey(), array(
            '%name%' => $data['userFullName']
        ));
        $notification->setMessage($translatedMessage);
        foreach ($friends as $friend) {
            $pushMessage = new PushMessage();
            $pushMessage->setNotification($notification);
            $pushMessage->setUser($friend);
            $notification->addPushMessage($pushMessage);
        }

        return $notification;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return ApiEvent::USER_CREATED;
    }

}