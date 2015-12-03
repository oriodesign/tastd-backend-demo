<?php

namespace Tastd\Bundle\CoreBundle\Manager;

use Tastd\Bundle\CoreBundle\Entity\Cuisine;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;

/**
 * Class RestaurantManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class RestaurantManager
{
    /**
     * Try to guess the cuisine by restaurant name
     * otherwise return random cuisine
     *
     * @param Restaurant $restaurant
     * @param array      $cuisines
     *
     * @return Cuisine
     */
    public function guessCuisine(Restaurant $restaurant, array $cuisines)
    {
        $name = strtolower($restaurant->getName());
        /** @var Cuisine $cuisine */
        foreach ($cuisines as $cuisine) {
            if (strpos($name, strtolower($cuisine->getName())) !== false) {
                return $cuisine;
            }
        }

        return $cuisines[array_rand($cuisines)];
    }
}