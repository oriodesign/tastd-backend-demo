<?php

namespace Tastd\Bundle\CoreBundle\Manager;
use Tastd\Bundle\CoreBundle\Entity\Wish;

/**
 * Class WishManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class WishManager
{

    /**
     * @param Wish $wish
     */
    public function deduceMissingFields(Wish $wish)
    {
        $restaurant = $wish->getRestaurant();
        $user = $wish->getUser();

        if (null === $user || null === $restaurant) {
            return;
        }

        if (null === $wish->getGeoname()) {
            $wish->setGeoname($restaurant->getGeoname());
        }

        if (null === $wish->getCuisine()) {
            $wish->setCuisine($restaurant->getCuisine());
        }

    }

}