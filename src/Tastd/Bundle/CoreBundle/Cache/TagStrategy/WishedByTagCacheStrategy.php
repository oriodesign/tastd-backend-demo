<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface WishedByTagCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class WishedByTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('wishedBy');
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = array();
        $geonameSuffix = $this->getGeonameSuffix($cacheMetaBag);
        $tags[] = CacheTag::REVIEWED_BY . $cacheMetaBag->getParameter('wishedBy') . $geonameSuffix;

        return $tags;
    }
}