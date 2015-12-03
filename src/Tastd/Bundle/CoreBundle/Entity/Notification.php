<?php

namespace Tastd\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Notification
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Notification';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Notification';

    const LEADER_FOLLOWS = 'leader.follows';
    const NEW_FOLLOWER = 'new.follower';
    const NEW_FACEBOOK_FRIEND = 'new.facebook.friend';
    const LEADER_WISH = 'leader.wish';
    const LEADER_REVIEW = 'leader.review';
    const NEW_TAGGED_FRIENDS = 'new.tagged.friends';

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var array */
    protected $content;
    /** @var \DateTime */
    protected $created;
    /** @var string */
    protected $queueId;
    /** @var ArrayCollection */
    protected $pushMessages;
    /** @var string */
    protected $message;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->pushMessages = new ArrayCollection();
        $this->message = '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return ArrayCollection
     */
    public function getPushMessages()
    {
        return $this->pushMessages;
    }

    /**
     * @param ArrayCollection $pushMessages
     */
    public function setPushMessages($pushMessages)
    {
        $this->pushMessages = $pushMessages;
    }

    /**
     * @param PushMessage $pushMessage
     */
    public function addPushMessage($pushMessage)
    {
        $this->pushMessages[] = $pushMessage;
    }

    /**
     * @return string
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * @param string $queueId
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;
    }

    public function getTranslationKey()
    {
        return 'notification.' . $this->name;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

}
