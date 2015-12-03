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
 * LeaderReviewNotificationFactory
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
class LeaderReviewNotificationFactory implements NotificationFactory
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
        $userId = $data['user'];
        $reviewId = $data['review'];
        $notification = New Notification();
        $notification->setName(Notification::LEADER_REVIEW);
        $notification->setContent(array(
            'review' => $reviewId,
            'restaurant' => $data['restaurant'],
            'restaurantName' => $data['restaurantName'],
            'user' => $data['user'],
            'userFullName' => $data['userFullName'],
            'image' => $data['image']
        ));
        $translatedMessage = $this->translator->trans($notification->getTranslationKey(), array(
            '%name%' => $data['userFullName'],
            '%restaurant%' => $data['restaurantName']
        ));
        $notification->setMessage($translatedMessage);
        $followersIds = $this->userRepository->getFollowersIds($userId);
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
        return ApiEvent::REVIEW_CREATED;
    }

}