<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Cuisine
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Cuisine
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Cuisine';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Cuisine';

    /** @var integer */
    protected $id;
    /** @var string */
    protected $prettyId;
    /** @var string */
    protected $name;
    /** @var string */
    protected $color;
    /** @var Wish[] */
    protected $wishes;
    /** @var Review[] */
    protected $reviews;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Set prettyId
     * @param string $prettyId
     *
     * @return Cuisine
     */
    public function setPrettyId($prettyId)
    {
        $this->prettyId = $prettyId;

        return $this;
    }

    /**
     * Get prettyId
     *
     * @return string 
     */
    public function getPrettyId()
    {
        return $this->prettyId;
    }

    /**
     * Set name
     * @param string $name
     *
     * @return Cuisine
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set color
     * @param string $color
     *
     * @return Cuisine
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Wish[]
     */
    public function getWishes()
    {
        return $this->wishes;
    }

    /**
     * @param Wish[] $wishes
     */
    public function setWishes($wishes)
    {
        $this->wishes = $wishes;
    }

    /**
     * @return Review[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param Review[] $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }


}
