<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Ranking
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Ranking
{

    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Ranking';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Ranking';

    /** @var array */
    public $reviews;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->reviews = array();
    }

    /**
     * @return array
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param array $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }


}