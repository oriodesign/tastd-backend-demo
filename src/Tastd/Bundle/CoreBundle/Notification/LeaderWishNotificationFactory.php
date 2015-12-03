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
 * LeaderWishNotificationFactory
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
class LeaderWishNotificationFactory implements NotificationFactory
{
    /** @var UserRepository */
    protected $userRepository;
    /** @var EntityManager */
    protected $entityManager;
    /** @var Translator */
    protected $translator;
    /** @var FrequencyFilter */
    protected $frequencyFilter;

    /**
     * @param UserRepository  $userRepository
     * @param EntityManager   $entityManager
     * @param Translator      $translator
     * @param FrequencyFilter $frequencyFilter
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManager $entityManager,
        Translator $translator,
        FrequencyFilter $frequencyFilter)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
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
        $notification->setName(Notification::LEADER_WISH);
        $notification->setContent(array(
            'wish' => $data['wish'],
            'userFullName' => $data['userFullName'],
            'user' => $data['user'],
            'restaurant' => $data['restaurant'],
            'restaurantName' => $data['restaurantName'],
            'image' => $data['image']
        ));
        $translatedMessage = $this->translator->trans($notification->getTranslationKey(), array(
            '%name%' => $data['userFullName'],
            '%restaurant%' => $data['restaurantName']
        ));
        $notification->setMessage($translatedMessage);
        $followersIds = $this->userRepository->getFollowersIdsWithWish($data['user'], $data['wish']);
        foreach ($followersIds as $followersId) {
            $this->addPushMessage($notification, $followersId);
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
        return ApiEvent::WISH_CREATED;
    }

}