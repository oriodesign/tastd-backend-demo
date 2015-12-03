<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Doctrine\Common\Cache\Cache;
use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\Option;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface OptionInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class OptionInvalidateCacheStrategy extends InvalidateCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof Option;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var Option $option */
        $option = $cacheMetaBag->getEntity();
        $user = $option->getUser();

        $tags[] = CacheTag::OPTION . $option->getId();
        $tags[] = $this->getEntityOfUserTag(CacheTag::OPTION, $user->getId());

        return $tags;
    }
}