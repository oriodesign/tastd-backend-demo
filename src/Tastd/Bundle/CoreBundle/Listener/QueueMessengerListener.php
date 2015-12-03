<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Tastd\Bundle\CoreBundle\Aws\SqsClient;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\ConnectionCreatedEvent;
use Tastd\Bundle\CoreBundle\Event\ReviewCreatedEvent;
use Tastd\Bundle\CoreBundle\Event\ReviewUpdatedEvent;
use Tastd\Bundle\CoreBundle\Event\UserCreatedEvent;
use Tastd\Bundle\CoreBundle\Event\WishCreatedEvent;

/**
 * Class QueueMessengerListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class QueueMessengerListener
{
    /** @var Serializer */
    protected $serializer;
    /** @var SqsClient */
    protected $sqsClient;
    /** @var SerializationContext */
    protected $serializationContext;

    /**
     * @param Serializer $serializer
     * @param SqsClient  $sqsClient
     */
    public function __construct(Serializer $serializer, SqsClient $sqsClient)
    {
        $this->serializer = $serializer;
        $this->sqsClient = $sqsClient;
        $this->serializationContext = new SerializationContext();
        $this->serializationContext->setSerializeNull(true);

    }

    /**
     * @param WishCreatedEvent $wishCreatedEvent
     */
    public function onWishCreated(WishCreatedEvent $wishCreatedEvent)
    {
        $wish = $wishCreatedEvent->getWish();
        $data = array(
            'event' => ApiEvent::WISH_CREATED,
            'wish' => $wish->getId(),
            'user' => $wish->getUser()->getId(),
            'userFullName' => $wish->getUser()->getFullName(),
            'restaurantName' => $wish->getRestaurant()->getName(),
            'restaurant' => $wish->getRestaurant()->getId(),
            'image' => $wish->getUser()->getAvatar()
        );

        $message = $this->serializer->serialize($data, 'json', $this->serializationContext);
        $this->sqsClient->send($message);
    }

    /**
     * @param ReviewCreatedEvent $reviewCreatedEvent
     */
    public function onReviewCreated(ReviewCreatedEvent $reviewCreatedEvent)
    {
        $review = $reviewCreatedEvent->getReview();
        $data = array(
            'event' => ApiEvent::REVIEW_CREATED,
            'review' => $review->getId(),
            'user' => $review->getUser()->getId(),
            'userFullName' => $review->getUser()->getFullName(),
            'restaurantName' => $review->getRestaurant()->getName(),
            'restaurant' => $review->getRestaurant()->getId(),
            'image' => $review->getUser()->getAvatar()
        );

        $message = $this->serializer->serialize($data, 'json', $this->serializationContext);
        $this->sqsClient->send($message);
    }

    /**
     * @param ReviewUpdatedEvent $reviewUpdatedEvent
     */
    public function onReviewUpdated(ReviewUpdatedEvent $reviewUpdatedEvent)
    {
        $review = $reviewUpdatedEvent->getNewReview();
        $oldReview = $reviewUpdatedEvent->getOldReview();
        $taggedFriendsIds = array();
        $alreadyTaggedFriendsIds = array();
        foreach ($review->getTaggedFriends() as $taggedFriend) {
            $taggedFriendsIds[] = $taggedFriend->getId();
        }
        foreach ($oldReview->getTaggedFriends() as $taggedFriend) {
            $alreadyTaggedFriendsIds[] = $taggedFriend->getId();
        }

        $newTaggedFriendsIds = array_diff($taggedFriendsIds, $alreadyTaggedFriendsIds);

        $data = array(
            'event' => ApiEvent::REVIEW_UPDATED,
            'review' => $review->getId(),
            'user' => $review->getUser()->getId(),
            'userFullName' => $review->getUser()->getFullName(),
            'restaurantName' => $review->getRestaurant()->getName(),
            'restaurant' => $review->getRestaurant()->getId(),
            'image' => $review->getUser()->getAvatar(),
            'newTaggedFriends' => implode(',', $newTaggedFriendsIds)
        );

        $message = $this->serializer->serialize($data, 'json', $this->serializationContext);
        $this->sqsClient->send($message);
    }



    /**
     * @param ConnectionCreatedEvent $connectionCreatedEvent
     */
    public function onConnectionCreated(ConnectionCreatedEvent $connectionCreatedEvent)
    {
        $connection = $connectionCreatedEvent->getConnection();
        $follower = $connection->getFollower();
        $leader = $connection->getLeader();

        $data = array(
            'event' => ApiEvent::CONNECTION_CREATED,
            'leader' => $leader->getId(),
            'follower' => $follower->getId(),
            'followerFullName' => $follower->getFullName(),
            'leaderFullName' => $leader->getFullName(),
            'leaderImage' => $leader->getAvatar(),
            'followerImage' => $follower->getAvatar()
        );

        $message = $this->serializer->serialize($data, 'json', $this->serializationContext);
        $this->sqsClient->send($message);
    }

    /**
     * @param UserCreatedEvent $userCreatedEvent
     */
    public function onUserCreated(UserCreatedEvent $userCreatedEvent)
    {
        $user = $userCreatedEvent->getUser();

        $data = array(
            'event' => ApiEvent::USER_CREATED,
            'user' => $user->getId(),
            'userFullName' => $user->getFullName(),
            'image' => $user->getAvatar()
        );

        $message = $this->serializer->serialize($data, 'json', $this->serializationContext);
        $this->sqsClient->send($message);
    }

}