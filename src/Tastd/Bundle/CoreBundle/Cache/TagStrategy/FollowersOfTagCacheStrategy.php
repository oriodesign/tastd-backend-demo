<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface FollowersOfTagCacheStrategyInterface
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class FollowersOfTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('followersOf');
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = array();
        $userId = $cacheMetaBag->getParameter('followersOf');
        $tags[] = $this->getEntityOfUserTag(CacheTag::FOLLOWER, $userId);

        return $tags;
    }
}