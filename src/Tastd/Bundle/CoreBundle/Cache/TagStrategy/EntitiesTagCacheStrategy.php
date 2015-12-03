<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;

/**
 * Class EntitiesOfUserTagCacheStrategyInterface
 *
 * @package Tastd\Bundle\CoreBundle\Cache\TagStrategy
 */
class EntitiesTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return true;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = array();
        foreach ($cacheMetaBag->getCacheTags() as $cacheTag) {
            $tags[] = $cacheTag;
        }

        return $tags;
    }

}