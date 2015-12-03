<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Connection;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface ConnectionInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class ConnectionInvalidateCacheStrategy extends InvalidateCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof Connection;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var Connection $connection */
        $connection = $cacheMetaBag->getEntity();
        $follower = $connection->getFollower();
        $leader = $connection->getLeader();

        $tags[] = $this->getEntityTag(CacheTag::CONNECTION, $connection->getId());
        $tags[] = $this->getEntityOfUserTag(CacheTag::FOLLOWER, $leader->getId());
        $tags[] = $this->getEntityOfUserTag(CacheTag::LEADER, $follower->getId());
        $tags[] = $this->getEntityTag(CacheTag::USER, $leader->getId());   // Because of counters
        $tags[] = $this->getEntityTag(CacheTag::USER, $follower->getId()); // Because of counters

        return $tags;
    }
}