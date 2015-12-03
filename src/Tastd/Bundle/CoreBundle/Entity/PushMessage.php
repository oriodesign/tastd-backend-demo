<?php

namespace Tastd\Bundle\CoreBundle\Entity;

class PushMessage
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:PushMessage';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\PushMessage';

    /** @var int */
    protected $id;
    /** @var User */
    protected $user;
    /** @var Notification */
    protected $notification;
    /** @var boolean */
    protected $seen;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->seen = false;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param Notification $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return boolean
     */
    public function isSeen()
    {
        return $this->seen;
    }

    /**
     * @param boolean $seen
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;
    }




}