<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class GeoScore
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class GeoScore
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:GeoScore';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\GeoScore';

    /** @var int */
    protected $id;
    /** @var User */
    protected $user;
    /** @var Geoname */
    protected $geoname;
    /** @var int */
    protected $score;
    /** @var int */
    protected $count;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->count = 0;
        $this->score = 0;
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
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }





}