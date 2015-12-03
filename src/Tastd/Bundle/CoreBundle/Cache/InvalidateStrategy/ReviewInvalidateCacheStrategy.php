<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface ReviewInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class ReviewInvalidateCacheStrategy extends InvalidateCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof Review;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var Review $review */
        $review = $cacheMetaBag->getEntity();
        $geoname = $review->getGeoname();
        $user = $review->getUser();
        $tags[] = $this->getEntityOfUserTag(CacheTag::REVIEW, $user->getId());
        $tags[] = $this->getEntityTag(CacheTag::REVIEW, $review->getId());
        $tags[] = $this->getEntityTag(CacheTag::USER, $user->getId()); // @TODO Because of the counter
        $tags[] = $this->getEntityOfUserInGeonameTag(CacheTag::REVIEW, $user->getId(), $geoname->getId());

        return $tags;
    }
}