<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Doctrine\ORM\Cache;
use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Connection;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * abstract class InvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
abstract class InvalidateCacheStrategy implements InvalidateCacheStrategyInterface
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
     * @param string $id
     *
     * @return string
     */
    protected function getEntityTag($tag, $id)
    {
        return $tag . $id;
    }

    /**
     * @param string $tag
     * @param string $userId
     * @param string $geonameId
     *
     * @return string
     */
    protected function getEntityOfUserInGeonameTag($tag, $userId, $geonameId)
    {
        return $tag . CacheTag::USER . $userId . CacheTag::GEONAME . $geonameId;
    }

    /**
     * @param $tag
     * @param $geonameId
     * @return string
     */
    protected function getEntityInGeonameTag($tag, $geonameId)
    {
        return $tag . CacheTag::GEONAME . $geonameId;
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
}