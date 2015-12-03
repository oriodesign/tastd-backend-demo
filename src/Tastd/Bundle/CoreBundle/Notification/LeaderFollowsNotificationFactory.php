<?php

namespace Tastd\Bundle\CoreBundle\Notification;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\Translator;
use Tastd\Bundle\CoreBundle\Entity\Notification;
use Tastd\Bundle\CoreBundle\Entity\PushMessage;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Notification\Filter\FrequencyFilter;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Interface LeaderFollowsNotificationFactory
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
class LeaderFollowsNotificationFactory implements NotificationFactory
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var UserRepository */
    protected $userRepository;
    /** @var Translator */
    protected $translator;
    /** @var FrequencyFilter */
    protected $frequencyFilter;

    /**
     * @param EntityManager   $entityManager
     * @param UserRepository  $userRepository
     * @param Translator      $translator
     * @param FrequencyFilter $frequencyFilter
     */
    public function __construct(
        EntityManager $entityManager,
        UserRepository $userRepository,
        Translator $translator,
        FrequencyFilter $frequencyFilter
    )
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->frequencyFilter = $frequencyFilter;
    }

    /**
     * @param array $data
     *
     * @return Notification
     */
    public function create($data)
    {
        $notification = New Notification();
        $notification->setName(Notification::LEADER_FOLLOWS);
        $notification->setContent(array(
            'follower' => $data['follower'],
            'followerFullName' => $data['followerFullName'],
            'leader' => $data['leader'],
            'leaderFullName' => $data['leaderFullName'],
            'image' => $data["followerImage"]
        ));
        $followersIds = $this->userRepository->getFollowersIds($data['follower']);
        $translatedMessage = $this->translator->trans($notification->getTranslationKey(), array(
            '%name%' => $data['followerFullName'],
            '%leader%' => $data['leaderFullName']
        ));
        $notification->setMessage($translatedMessage);

        foreach ($followersIds as $followerId) {
            $this->addPushMessage($notification, $followerId);
        }

        return $notification;
    }

    /**
     * @param Notification $notification
     * @param int          $userId
     */
    protected function addPushMessage(Notification $notification, $userId)
    {
        if ($this->frequencyFilter->filter($userId)) {
            return;
        }
        $this->frequencyFilter->addPushedUser($userId);
        $pushMessage = new PushMessage();
        $pushMessage->setNotification($notification);
        $pushMessage->setUser($this->entityManager->getReference(User::CLASS_NAME, $userId));
        $notification->addPushMessage($pushMessage);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return ApiEvent::CONNECTION_CREATED;
    }

}