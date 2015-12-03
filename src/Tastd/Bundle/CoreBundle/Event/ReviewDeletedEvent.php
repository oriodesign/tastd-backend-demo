<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class ReviewDeletedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class ReviewDeletedEvent extends ApiEvent
{
    /** @var Review */
    protected $review;

    /**
     * @param Review $review
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * @return Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->review->getUser();
    }

}