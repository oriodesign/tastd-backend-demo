<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;

/**
 * Class EntitiesOfGeonameTagCacheStrategyInterface
 *
 * @package Tastd\Bundle\CoreBundle\Cache\TagStrategy
 */
class EntitiesOfGeonameTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('geoname');
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $geonameSuffix = $this->getGeonameSuffix($cacheMetaBag);
        $tags = array();
        foreach ($cacheMetaBag->getCacheTags() as $cacheTag) {
            $tags[] = $cacheTag . $geonameSuffix;
        }

        return $tags;
    }

}