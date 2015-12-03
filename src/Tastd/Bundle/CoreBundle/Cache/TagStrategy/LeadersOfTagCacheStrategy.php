<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface LeaderOfTagCacheStrategyInterface
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class LeadersOfTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('leadersOf');
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = array();
        $userId = $cacheMetaBag->getParameter('leadersOf');
        $tags[] = $this->getEntityOfUserTag(CacheTag::LEADER, $userId);

        return $tags;
    }
}