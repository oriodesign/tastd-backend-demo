<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Class TagCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\TagStrategy
 */
abstract class TagCacheStrategy implements TagCacheStrategyInterface
{

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return string
     */
    protected function getGeonameSuffix(CacheMetaBag $cacheMetaBag)
    {
        $geonameId = $cacheMetaBag->getParameter('geoname');
        if ($geonameId) {
            return CacheTag::GEONAME . $geonameId;
        }

        return '';
    }

    /**
     * @param string $tag
     * @param string $userId
     *
     * @return string
     */
    protected function getEntityOfUserTag($tag, $userId)
    {
        return $tag . CacheTag::USER . $userId;
    }

    /**
     * @param string $tag
     * @param string $userId
     * @param string $geonameSuffix
     *
     * @return string
     */
    protected function getEntityOfUserInGeonameTag($tag, $userId, $geonameSuffix)
    {
        return $tag . CacheTag::USER . $userId . $geonameSuffix;
    }

}