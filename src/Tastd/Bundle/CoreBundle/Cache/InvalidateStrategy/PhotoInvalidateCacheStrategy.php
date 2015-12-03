<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Photo;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface PhotoInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class PhotoInvalidateCacheStrategy extends InvalidateCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof Photo;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var Photo $photo */
        $photo = $cacheMetaBag->getEntity();
        $user = $photo->getUser();
        $geoname = $photo->getRestaurant()->getGeoname();

        $tags[] = CacheTag::PHOTO . $user->getId();
        $tags[] = $this->getEntityOfUserTag(CacheTag::REVIEW, $user->getId());
        $tags[] = $this->getEntityOfUserInGeonameTag(CacheTag::REVIEW, $user->getId(), $geoname->getId());

        return $tags;
    }
}