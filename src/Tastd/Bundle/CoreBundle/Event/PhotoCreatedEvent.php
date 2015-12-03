<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Photo;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class RestaurantCreatedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class PhotoCreatedEvent extends ApiEvent
{
    /** @var Photo */
    protected $photo;
    /** @var User $user */
    protected $user;

    /**
     * @param Photo      $photo
     * @param User       $user
     */
    public function __construct(Photo $photo, User $user)
    {
        $this->photo = $photo;
        $this->user = $user;
    }

    /**
     * @return Photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return array();
    }

}