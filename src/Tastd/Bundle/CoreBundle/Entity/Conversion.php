<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Conversion
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Conversion
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Conversion';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Conversion';

    /** @var integer */
    protected $id;
    /** @var Invite */
    protected $invite;
    /** @var \DateTime  */
    protected $created;
    /** @var string */
    protected $fingerprint;
    /** @var User */
    protected $user;

    /**
     * construct
     */
    public function __construct()
    {
        $this->created = new \DateTime();
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
     * @return mixed
     */
    public function getInvite()
    {
        return $this->invite;
    }

    /**
     * @param mixed $invite
     */
    public function setInvite($invite)
    {
        $this->invite = $invite;
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



}