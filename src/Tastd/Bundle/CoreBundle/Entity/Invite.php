<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Invite
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Invite
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Invite';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Invite';

    /** @var integer */
    protected $id;
    /** @var User */
    protected $user;
    /** @var string */
    protected $code;
    /** @var string */
    protected $recipients;
    /** @var string */
    protected $channel;
    /** @var \DateTime  */
    protected $created;

    /**
     * construct
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->code = md5(uniqid(rand(), true));
    }

    /**
     * @return string
     */
    public function toString()
    {
        return (string)$this->code;
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
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param string $recipients
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }



}