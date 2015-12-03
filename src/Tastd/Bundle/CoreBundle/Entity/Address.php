<?php

namespace Tastd\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Address
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Address
{

    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Address';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Address';

    protected $id;
    protected $geoname;
    protected $formattedAddress;
    protected $country;
    protected $countryCode;
    protected $region;
    protected $regionCode;
    protected $county;
    protected $countyCode;
    protected $city;
    protected $streetName;
    protected $streetNumber;
    protected $postalCode;
    protected $lat;
    protected $lng;
    protected $created;
    protected $updated;

    /**
     * Constructor
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
        return (string)$this->formattedAddress;
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
     * Set formattedAddress
     * @param string $formattedAddress
     *
     * @return Address
     */
    public function setFormattedAddress($formattedAddress)
    {
        $this->formattedAddress = $formattedAddress;

        return $this;
    }

    /**
     * Get formattedAddress
     *
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formattedAddress;
    }

    /**
     * Set country
     * @param string $country
     *
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $countryCode
     *
     * @return Address
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $county
     *
     * @return Address
     */
    public function setCounty($county)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @param mixed $countyCode
     *
     * @return Address
     */
    public function setCountyCode($countyCode)
    {
        $this->countyCode = $countyCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountyCode()
    {
        return $this->countyCode;
    }

    /**
     * @param mixed $region
     *
     * @return Address
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $regionCode
     *
     * @return Address
     */
    public function setRegionCode($regionCode)
    {
        $this->regionCode = $regionCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * Set city
     * @param string $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set streetName
     * @param string $streetName
     *
     * @return Address
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Get streetName
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set streetNumber
     * @param string $streetNumber
     *
     * @return Address
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     * Get streetNumber
     *
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * Set postalCode
     * @param string $postalCode
     *
     * @return Address
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set lat
     * @param float $lat
     *
     * @return Address
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return Address
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }


    /**
     * Set created
     * @param \DateTime $created
     *
     * @return Address
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     * @param \DateTime $updated
     *
     * @return Address
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * onPreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
    }
}
