<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Wish
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Wish
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Wish';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Wish';

    /** @var int */
    protected $id;
    /** @var User */
    protected $user;
    /** @var Restaurant */
    protected $restaurant;
    /** @var Cuisine */
    protected $cuisine;
    /** @var Geoname */
    protected $geoname;
    /** @var \DateTime */
    protected $created;
    /** @var \DateTime */
    protected $updated;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * onPreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->restaurant;
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
     * @return Cuisine
     */
    public function getCuisine()
    {
        return $this->cuisine;
    }

    /**
     * @param Cuisine $cuisine
     */
    public function setCuisine($cuisine)
    {
        $this->cuisine = $cuisine;
    }

    /**
     * @return Geoname
     */
    public function getGeoname()
    {
        return $this->geoname;
    }

    /**
     * @param Geoname $geoname
     */
    public function setGeoname($geoname)
    {
        $this->geoname = $geoname;
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