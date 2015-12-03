<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use Tastd\Bundle\CoreBundle\Event\InviteCreatedEvent;
use Tastd\Bundle\CoreBundle\Facebook\FacebookClient;
use Tastd\Bundle\CoreBundle\Key\InviteChannel;
use Tastd\Bundle\CoreBundle\Mailer\Mailer;

/**
 * Class CreateInviteListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class CreateInviteListener
{
    /** @var Mailer */
    protected $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param InviteCreatedEvent $inviteCreatedEvent
     */
    public function onCreateInvite(InviteCreatedEvent $inviteCreatedEvent)
    {
        $invite = $inviteCreatedEvent->getInvite();
        if ($invite->getChannel() === InviteChannel::EMAIL) {
            $this->mailer->sendInviteEmail($invite);
        }
    }
}