<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface WishInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class WishInvalidateCacheStrategy extends InvalidateCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof Wish;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var Wish $wish */
        $wish = $cacheMetaBag->getEntity();
        $geoname = $wish->getGeoname();
        $user = $wish->getUser();

        $tags[] = $this->getEntityOfUserTag(CacheTag::WISH, $user->getId());
        $tags[] = $this->getEntityTag(CacheTag::WISH, $wish->getId());
        $tags[] = $this->getEntityTag(CacheTag::USER, $user->getId()); // @TODO Because of the counter
        $tags[] = $this->getEntityOfUserInGeonameTag(CacheTag::WISH, $user->getId(), $geoname->getId());


        return $tags;
    }
}