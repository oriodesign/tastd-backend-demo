<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class ReviewCreatedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class ReviewCreatedEvent extends ApiEvent
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

    /**
     * @return array
     */
    public function getMeta()
    {
        return array(
            'coordinate' => array(
                $this->review->getRestaurant()->getLng(),
                $this->review->getRestaurant()->getLat(),
            )
        );
    }

}