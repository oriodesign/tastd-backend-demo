<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Photo;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class PhotoDeletedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class PhotoDeletedEvent extends ApiEvent
{
    /** @var Review */
    protected $review;

    /**
     * @param Photo $photo
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
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
        return $this->photo->getUser();
    }

}