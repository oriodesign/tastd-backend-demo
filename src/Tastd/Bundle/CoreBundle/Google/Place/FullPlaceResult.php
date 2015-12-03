<?php

namespace Tastd\Bundle\CoreBundle\Google\Place;
use Tastd\Bundle\CoreBundle\Entity\Geoname;

/**
 * Class FullPlaceResult
 *
 * @package Tastd\Bundle\CoreBundle\Google\Place
 */
class FullPlaceResult extends PlaceResult
{
    /** @var string */
    protected $streetName;
    /** @var string */
    protected $streetNumber;
    /** @var string */
    protected $country;
    /** @var string */
    protected $countryCode;
    /** @var string */
    protected $region;
    /** @var string */
    protected $regionCode;
    /** @var string */
    protected $county;
    /** @var string */
    protected $countyCode;
    /** @var string */
    protected $postalCode;
    /** @var string */
    protected $city;
    /** @var array */
    protected $photos;
    /** @var string */
    protected $telephone;
    /** @var string */
    protected $website;
    /** @var string */
    protected $formattedAddress;
    /** @var float */
    protected $latitude;
    /** @var float */
    protected $longitude;
    /** @var string */
    protected $photoReferences;
    /** @var Geoname */
    protected $geoname;

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param array $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * @return array
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $streetName
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $formattedAddress
     */
    public function setFormattedAddress($formattedAddress)
    {
        $this->formattedAddress = $formattedAddress;
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formattedAddress;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $photoReferences
     */
    public function setPhotoReferences($photoReferences)
    {
        $this->photoReferences = $photoReferences;
    }

    /**
     * @return string
     */
    public function getPhotoReferences()
    {
        return $this->photoReferences;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $county
     */
    public function setCounty($county)
    {
        $this->county = $county;
    }

    /**
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @param string $countyCode
     */
    public function setCountyCode($countyCode)
    {
        $this->countyCode = $countyCode;
    }

    /**
     * @return string
     */
    public function getCountyCode()
    {
        return $this->countyCode;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $regionCode
     */
    public function setRegionCode($regionCode)
    {
        $this->regionCode = $regionCode;
    }

    /**
     * @return string
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * @return \Tastd\Bundle\CoreBundle\Entity\Geoname
     */
    public function getGeoname()
    {
        return $this->geoname;
    }

    /**
     * @param \Tastd\Bundle\CoreBundle\Entity\Geoname $geoname
     */
    public function setGeoname($geoname)
    {
        $this->geoname = $geoname;
    }




}