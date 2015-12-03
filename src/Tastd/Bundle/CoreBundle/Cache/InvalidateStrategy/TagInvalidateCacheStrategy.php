<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Tag;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface TagInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class TagInvalidateCacheStrategy implements InvalidateCacheStrategyInterface
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof Tag;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var Tag $tag */
        $tag = $cacheMetaBag->getEntity();

        $tags[] = CacheTag::TAG;

        return $tags;
    }
}