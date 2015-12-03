<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Flag
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Flag
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Flag';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Flag';

    protected $position;
    protected $lat;
    protected $lng;
    protected $name;
    protected $restaurantId;
    protected $cuisineName;
    protected $formattedAddress;
    protected $color;
    protected $picture;
    protected $cuisineId;


    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $restaurantId
     */
    public function setRestaurantId($restaurantId)
    {
        $this->restaurantId = $restaurantId;
    }

    /**
     * @return mixed
     */
    public function getRestaurantId()
    {
        return $this->restaurantId;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $cuisineName
     */
    public function setCuisineName($cuisineName)
    {
        $this->cuisineName = $cuisineName;
    }

    /**
     * @return mixed
     */
    public function getCuisineName()
    {
        return $this->cuisineName;
    }

    /**
     * @param mixed $formattedAddress
     */
    public function setFormattedAddress($formattedAddress)
    {
        $this->formattedAddress = $formattedAddress;
    }

    /**
     * @return mixed
     */
    public function getFormattedAddress()
    {
        return $this->formattedAddress;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @return mixed
     */
    public function getCuisineId()
    {
        return $this->cuisineId;
    }

    /**
     * @param mixed $cuisineId
     */
    public function setCuisineId($cuisineId)
    {
        $this->cuisineId = $cuisineId;
    }



}