<?php

namespace Tastd\Bundle\CoreBundle\Google\Place;

/**
 * Class PlaceResult
 *
 * @package Tastd\Bundle\CoreBundle\Google\Place
 */
class AutocompleteResult
{
    /** @var integer */
    protected $id;
    /** @var string */
    protected $name;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



}