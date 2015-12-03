<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class ReviewUpdateEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class ReviewUpdatedEvent extends ApiEvent
{
    /** @var Review */
    protected $oldReview;
    /** @var Review */
    protected $newReview;

    /**
     * @param Review $oldReview
     * @param Review $newReview
     */
    public function __construct(Review $oldReview, Review $newReview)
    {
        $this->oldReview = $oldReview;
        $this->newReview = $newReview;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\Review $newReview
     */
    public function setNewReview($newReview)
    {
        $this->newReview = $newReview;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Review
     */
    public function getNewReview()
    {
        return $this->newReview;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\Review $oldReview
     */
    public function setOldReview($oldReview)
    {
        $this->oldReview = $oldReview;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Review
     */
    public function getOldReview()
    {
        return $this->oldReview;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->oldReview->getUser();
    }

}