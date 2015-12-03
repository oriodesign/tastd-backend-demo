<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class ApiEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
abstract class ApiEvent extends Event
{
    const USER_CREATED = 'tastd.user.created';
    const USER_UPDATED = 'tastd.user.updated';
    const USER_DELETED = 'tastd.user.deleted';

    const CONNECTION_CREATED = 'tastd.connection.created';
    const CONNECTION_UPDATED = 'tastd.connection.updated';
    const CONNECTION_DELETED = 'tastd.connection.deleted';

    const REVIEW_CREATED = 'tastd.review.created';
    const REVIEW_UPDATED = 'tastd.review.updated';
    const REVIEW_DELETED = 'tastd.review.deleted';

    const PHOTO_CREATED = 'tastd.photo.created';
    const PHOTO_DELETED = 'tastd.photo.deleted';

    const WISH_CREATED = 'tastd.wish.created';
    const WISH_DELETED = 'tastd.wish.deleted';

    const RESTAURANT_CREATED = 'tastd.restaurant.created';

    const MESSAGE_CREATED = 'tastd.message.created';

    const INVITE_CREATED = 'tastd.invite.created';

    /**
     * @return User
     */
    abstract public function getUser();

    /**
     * @return array
     */
    public function getMeta()
    {
        return null;
    }

}