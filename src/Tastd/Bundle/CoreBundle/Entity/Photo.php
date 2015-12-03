<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Photo
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Photo
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Photo';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Photo';

    /** @var int */
    protected $id;
    /** @var User */
    protected $user;
    /** @var Restaurant */
    protected $restaurant;
    /** @var string */
    protected $src;
    /** @var string */
    protected $thumb;
    /** @var \DateTime */
    protected $created;
    /** @var \DateTime */
    protected $updated;
    protected $uploadedPicture;

    /**
     * __construct
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
        return $this->src;
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
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param string $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * @return string
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param string $thumb
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param Restaurant $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
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

    /**
     * @return mixed
     */
    public function getUploadedPicture()
    {
        return $this->uploadedPicture;
    }

    /**
     * @param mixed $uploadedPicture
     */
    public function setUploadedPicture($uploadedPicture)
    {
        $this->uploadedPicture = $uploadedPicture;
    }



}
