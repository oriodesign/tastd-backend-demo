<?php

namespace Tastd\Bundle\CoreBundle\Cache\TagStrategy;

use Tastd\Bundle\CoreBundle\Cache\CacheMetaBag;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class EntitiesOfLeadersOfTagCacheStrategyInterface
 *
 * @package Tastd\Bundle\CoreBundle\Cache\TagStrategy
 */
class EntitiesOfLeadersOfTagCacheStrategy extends TagCacheStrategy
{
    /** @var UserRepository $userRepository */
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct (UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     *
     * @return boolean
     */
    public function canTag(CacheMetaBag $cacheMetaBag)
    {
        return $cacheMetaBag->hasParameter('leadersOf');
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
        $userId = $cacheMetaBag->getParameter('leadersOf');
        $leadersIds = $this->userRepository->getLeadersIds($userId);

        foreach ($cacheMetaBag->getCacheTags() as $cacheTag) {
            foreach ($leadersIds as $leaderId) {
                $tags[] = $this->getEntityOfUserInGeonameTag($cacheTag, $leaderId, $geonameSuffix);
            }
        }

        return $tags;
    }


}