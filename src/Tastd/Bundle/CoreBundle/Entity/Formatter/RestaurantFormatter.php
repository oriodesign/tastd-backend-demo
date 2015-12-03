<?php

namespace Tastd\Bundle\CoreBundle\Entity\Formatter;

use Tastd\Bundle\CoreBundle\Entity\Restaurant;

class RestaurantFormatter implements EntityFormatterInterface
{
    /**
     * @param mixed $entity
     *
     * @return string
     */
    public function getOneLineDescription($entity)
    {
        /** @var Restaurant $restaurant */
        $restaurant = $entity;
        $template = '<info>%s</info> <fg=yellow>%s</> %s, %s <fg=blue>[%s]</> (%s,%s) %s - %s';

        return sprintf($template,
            $restaurant->getName(),
            $restaurant->getCuisine()->getName(),
            $restaurant->getGeoname()->getAsciiName(),
            $restaurant->getAddress(),
            $restaurant->getAverageCost(),
            $restaurant->getLat(),
            $restaurant->getLng(),
            $restaurant->getWebsite(),
            $restaurant->getPhone()
        );
    }

    /**
     * @param mixed $entity
     *
     * @return string
     */
    public function getOneLineShortDescription($entity)
    {
        /** @var Restaurant $restaurant */
        $restaurant = $entity;
        $template = '<info>%s</info> <fg=yellow>%s</> %s, %s';

        return sprintf($template,
            $restaurant->getName(),
            $restaurant->getCuisine()->getName(),
            $restaurant->getGeoname()->getAsciiName(),
            $restaurant->getAddress()
        );
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return Restaurant::CLASS_NAME;
    }
}