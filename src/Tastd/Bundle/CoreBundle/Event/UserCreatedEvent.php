<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class UserCreateEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class UserCreatedEvent extends ApiEvent
{
    /** @var User */
    protected $user;
    /** @var string */
    protected $fingerprint;

    /**
     * @param User   $user
     * @param string $fingerprint
     */
    public function __construct(User $user, $fingerprint = null)
    {
        $this->user = $user;
        $this->fingerprint = $fingerprint;
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

    /**
     * @return string
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * @param string $fingerprint
     */
    public function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;
    }
}