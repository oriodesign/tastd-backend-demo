<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;

/**
 * Class EntitiesOfUserTagCacheStrategyInterface
 *
 * @package Tastd\Bundle\CoreBundle\Cache\TagStrategy
 */
class EntitiesOfUserTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('user');
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $geonameSuffix = $this->getGeonameSuffix($cacheMetaBag);
        $userId = $cacheMetaBag->getParameter('user');
        $tags = array();
        foreach ($cacheMetaBag->getCacheTags() as $cacheTag) {
            $tags[] = $this->getEntityOfUserInGeonameTag($cacheTag, $userId, $geonameSuffix);
        }

        return $tags;
    }

}