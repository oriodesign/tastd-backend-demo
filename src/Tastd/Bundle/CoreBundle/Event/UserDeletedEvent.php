<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class UserDeleteEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class UserDeletedEvent extends ApiEvent
{
    /** @var User */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}