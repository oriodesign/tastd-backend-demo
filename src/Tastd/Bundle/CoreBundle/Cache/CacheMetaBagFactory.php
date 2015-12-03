<?php

namespace Tastd\Bundle\CoreBundle\Cache;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Class CacheMetaBagFactory
 *
 * @package Tastd\Bundle\CoreBundle\Cache
 */
class CacheMetaBagFactory
{

    /**
     * @param Request       $request
     * @param string|array  $cacheTags
     * @param boolean       $isCollection
     *
     * @return CacheMetaBag
     */
    public function create (Request $request, $cacheTags, $isCollection)
    {
        $cacheMetaBag = new CacheMetaBag();
        $cacheMetaBag->setRequest($request);
        $cacheMetaBag->setCacheTags($cacheTags);
        $cacheMetaBag->isCollection($isCollection);

        return $cacheMetaBag;
    }

    /**
     * @param mixed $entity
     *
     * @return CacheMetaBag
     */
    public function createInsertMetaBag($entity)
    {
        $cacheMetaBag = new CacheMetaBag();
        $cacheMetaBag->setEntity($entity);
        $cacheMetaBag->setCacheTags(array(CacheTag::INSERT));

        return $cacheMetaBag;
    }

    /**
     * @param mixed $entity
     *
     * @return CacheMetaBag
     */
    public function createDeleteMetaBag($entity)
    {
        $cacheMetaBag = new CacheMetaBag();
        $cacheMetaBag->setEntity($entity);
        $cacheMetaBag->setCacheTags(array(CacheTag::INSERT));

        return $cacheMetaBag;
    }

    /**
     * @param mixed $entity
     *
     * @return CacheMetaBag
     */
    public function createUpdateMetaBag($entity)
    {
        $cacheMetaBag = new CacheMetaBag();
        $cacheMetaBag->setEntity($entity);
        $cacheMetaBag->setCacheTags(array(CacheTag::INSERT));

        return $cacheMetaBag;
    }
}