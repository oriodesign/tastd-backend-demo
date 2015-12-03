<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class UserUpdateEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class UserUpdatedEvent extends ApiEvent
{
    /** @var User */
    protected $oldUser;
    /** @var User */
    protected $newUser;

    /**
     * @param User $oldUser
     * @param User $newUser
     */
    public function __construct(User $oldUser, User $newUser)
    {
        $this->oldUser = $oldUser;
        $this->newUser = $newUser;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\User $newUser
     */
    public function setNewUser($newUser)
    {
        $this->newUser = $newUser;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\User
     */
    public function getNewUser()
    {
        return $this->newUser;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\User $oldUser
     */
    public function setOldUser($oldUser)
    {
        $this->oldUser = $oldUser;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\User
     */
    public function getOldUser()
    {
        return $this->oldUser;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->oldUser;
    }

}