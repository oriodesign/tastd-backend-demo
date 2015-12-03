<?php

namespace Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Entity\PushMessage;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Interface PushMessageInvalidateCacheStrategy
 *
 * @package Tastd\Bundle\CoreBundle\Cache\Strategy
 */
class PushMessageInvalidateCacheStrategy extends InvalidateCacheStrategy
{
    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canInvalidate(CacheMetaBag $cacheMetaBag)
    {
        $entity = $cacheMetaBag->getEntity();

        return $entity instanceof PushMessage;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return array
     */
    public function getTags(CacheMetaBag $cacheMetaBag)
    {
        $tags = [];
        /** @var PushMessage $pushMessage */
        $pushMessage = $cacheMetaBag->getEntity();
        $user = $pushMessage->getUser();

        $tags[] = $this->getEntityOfUserTag(CacheTag::PUSH_MESSAGE, $user->getId());

        return $tags;
    }
}