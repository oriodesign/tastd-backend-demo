<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class GeoTranslation
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class GeoTranslation
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:GeoTranslation';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\GeoTranslation';

    /** @var int  */
    protected $id;
    /** @var Geoname  */
    protected $geoname;
    /** @var string */
    protected $isoLanguage;
    /** @var string */
    protected $alternateName;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->alternateName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return string
     */
    public function getIsoLanguage()
    {
        return $this->isoLanguage;
    }

    /**
     * @param string $isoLanguage
     */
    public function setIsoLanguage($isoLanguage)
    {
        $this->isoLanguage = $isoLanguage;
    }

    /**
     * @return string
     */
    public function getAlternateName()
    {
        return $this->alternateName;
    }

    /**
     * @param string $alternateName
     */
    public function setAlternateName($alternateName)
    {
        $this->alternateName = $alternateName;
    }


}