<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use Tastd\Bundle\CoreBundle\Event\MessageCreatedEvent;
use Tastd\Bundle\CoreBundle\Mailer\Mailer;

/**
 * Class CreateMessageListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class CreateMessageListener
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
     * @param MessageCreatedEvent $messageCreatedEvent
     */
    public function onMessageCreated(MessageCreatedEvent $messageCreatedEvent)
    {
        $message = $messageCreatedEvent->getMessage();
        $title = sprintf('[TASTD %s] %s ', $message->getCategory(), $message->getContent());
        $content = $message->getContent();

        $this->mailer->sendMessageToAdmin($title, $content);
    }
}