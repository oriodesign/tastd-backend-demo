<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface ReviewedByTagCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class ReviewedByTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('reviewedBy');
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = array();
        $userId = $cacheMetaBag->getParameter('reviewedBy');
        $geonameSuffix = $this->getGeonameSuffix($cacheMetaBag);
        $tags[] = CacheTag::REVIEWED_BY . $userId . $geonameSuffix;

        return $tags;
    }
}