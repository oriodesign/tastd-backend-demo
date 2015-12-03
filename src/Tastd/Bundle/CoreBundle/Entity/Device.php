<?php

namespace Tastd\Bundle\CoreBundle\Entity;

class Device
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Device';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Device';

    /** @var int */
    protected $id;
    /** @var User */
    protected $user;
    /** @var string */
    protected $name;
    /** @var string */
    protected $token;
    /** @var \DateTime */
    protected $created;
    /** @var \DateTime */
    protected $updated;


    /**
     * Construct
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }


    /**
     * onPreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
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
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }


}