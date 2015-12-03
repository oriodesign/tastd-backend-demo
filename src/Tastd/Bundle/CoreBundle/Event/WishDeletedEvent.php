<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class WishDeletedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class WishDeletedEvent extends ApiEvent
{
    /** @var Wish */
    protected $wish;

    /**
     * @param Wish $wish
     */
    public function __construct(Wish $wish)
    {
        $this->wish = $wish;
    }

    /**
     * @return Wish
     */
    public function getWish()
    {
        return $this->wish;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->wish->getUser();
    }

}