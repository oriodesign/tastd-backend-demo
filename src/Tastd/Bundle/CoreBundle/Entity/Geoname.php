<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class City
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Geoname
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Geoname';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Geoname';

    /** @var int  */
    protected $id;
    /** @var string  */
    protected $asciiName;
    /** @var float  */
    protected $lat;
    /** @var float  */
    protected $lng;
    /** @var string  */
    protected $country;
    /** @var string  */
    protected $admin1;
    /** @var int  */
    protected $population;
    /** @var string  */
    protected $timezone;
    /** @var \DateTime  */
    protected $modified;
    /** @var array */
    protected $translations;
    /** @var string */
    protected $formattedName;
    /** @var string */
    protected $currencySymbol;
    /** @var string */
    protected $currencyCode;
    /** @var Review[]  */
    protected $reviews;
    /** @var Wish[] */
    protected $wishes;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->asciiName;
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
    public function getAsciiName()
    {
        return $this->asciiName;
    }

    /**
     * @param string $asciiName
     */
    public function setAsciiName($asciiName)
    {
        $this->asciiName = $asciiName;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param float $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return int
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * @param int $population
     */
    public function setPopulation($population)
    {
        $this->population = $population;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param \DateTime $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param array $translations
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    /**
     * @return string
     */
    public function getFormattedName()
    {
        return $this->formattedName;
    }

    /**
     * @param string $formattedName
     */
    public function setFormattedName($formattedName)
    {
        $this->formattedName = $formattedName;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * @param string $currencySymbol
     */
    public function setCurrencySymbol($currencySymbol)
    {
        $this->currencySymbol = $currencySymbol;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getAdmin1()
    {
        return $this->admin1;
    }

    /**
     * @param string $admin1
     */
    public function setAdmin1($admin1)
    {
        $this->admin1 = $admin1;
    }

    /**
     * @return bool
     */
    public function isMetropolis()
    {
        return $this->population > 500000;
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



}