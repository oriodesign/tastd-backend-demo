<?php

namespace Tastd\Bundle\CoreBundle\Google\Place;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class PlaceResultCollection
 *
 * @package Tastd\Bundle\CoreBundle\Google\Place
 */
class PlaceResultCollection extends ArrayCollection
{
    protected $nextPage;

    /**
     * @param mixed $nextPage
     */
    public function setNextPage($nextPage)
    {
        $this->nextPage = $nextPage;
    }

    /**
     * @return mixed
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }



}