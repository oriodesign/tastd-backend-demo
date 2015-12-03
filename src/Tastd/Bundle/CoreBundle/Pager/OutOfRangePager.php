<?php

namespace Tastd\Bundle\CoreBundle\Pager;

use Pagerfanta\Pagerfanta;

/**
 * Class OutOfRangePager
 *
 * @package Tastd\Bundle\CoreBundle\Pager
 */
class OutOfRangePager extends PagerFanta
{
    /** @var int  */
    protected $originalPage;

    /**
     * @return int
     */
    public function getOriginalPage()
    {
        return $this->originalPage;
    }

    /**
     * @param int $originalPage
     */
    public function setOriginalPage($originalPage)
    {
        $this->originalPage = $originalPage;
    }
}