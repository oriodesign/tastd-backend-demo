<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Message;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class MessageCreatedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class MessageCreatedEvent extends ApiEvent
{

    /** @var Message */
    protected $message;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @param Message $message
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Message
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->message->getUser();
    }
}