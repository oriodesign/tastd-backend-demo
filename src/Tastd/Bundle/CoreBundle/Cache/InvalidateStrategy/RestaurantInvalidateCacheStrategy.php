<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface RestaurantInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class RestaurantInvalidateCacheStrategy extends InvalidateCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof Restaurant;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var Restaurant $restaurant */
        $restaurant = $cacheMetaBag->getEntity();
        $geoname = $restaurant->getGeoname();
        $tags[] = $this->getEntityInGeonameTag(CacheTag::RESTAURANT, $geoname->getId());

        return $tags;
    }
}