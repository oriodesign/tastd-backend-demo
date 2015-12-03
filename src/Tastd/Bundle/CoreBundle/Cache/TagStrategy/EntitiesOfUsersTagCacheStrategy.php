<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;

/**
 * Class EntitiesOfUsersTagCacheStrategyInterface
 *
 * @package Tastd\Bundle\CoreBundle\Cache\TagStrategy
 */
class EntitiesOfUsersTagCacheStrategy extends TagCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('users');
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
        $param = $cacheMetaBag->getParameter('users');
        $usersIds = explode(',', $param);

        foreach ($cacheMetaBag->getCacheTags() as $cacheTag) {
            foreach ($usersIds as $userId) {
                $tags[] = $this->getEntityOfUserInGeonameTag($cacheTag, $userId, $geonameSuffix);
            }
        }

        return $tags;
    }


}