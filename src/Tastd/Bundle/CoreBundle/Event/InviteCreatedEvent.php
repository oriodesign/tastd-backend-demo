<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Invite;
use Tastd\Bundle\CoreBundle\Entity\Message;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class InviteCreatedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class InviteCreatedEvent extends ApiEvent
{

    /** @var Invite */
    protected $invite;

    /**
     * @param Invite $invite
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * @param Invite $invite
     */
    public function setInvite(Invite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Invite
     */
    public function getInvite()
    {
        return $this->invite;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->invite->getUser();
    }
}