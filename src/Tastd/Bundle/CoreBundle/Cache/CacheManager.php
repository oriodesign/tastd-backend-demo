<?php

namespace Tastd\Bundle\CoreBundle\Cache;

use FOS\HttpCacheBundle\Handler\TagHandler;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Cache\InvalidateStrategy\InvalidateCacheStrategyInterface;
use Tastd\Bundle\CoreBundle\Cache\TagStrategy\TagCacheStrategyInterface;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;

/**
 * Class CacheManager
 *
 * @package Tastd\Bundle\CoreBundle\Cache
 */
class CacheManager
{
    /** @var TagHandler */
    protected $tagHandler;
    /** @var bool */
    protected $cacheEnabled;
    /** @var array  */
    protected $tagCacheStrategies;
    /** @var array */
    protected $invalidateCacheStrategies;
    /** @var CacheMetaBagFactory  */
    protected $cacheMetaBagFactory;

    /**
     * @param TagHandler          $tagHandler
     * @param CacheMetaBagFactory $cacheMetaBagFactory
     * @param boolean             $cacheEnabled
     */
    public function __construct(
        TagHandler $tagHandler,
        CacheMetaBagFactory $cacheMetaBagFactory,
        $cacheEnabled)
    {
        $this->tagHandler = $tagHandler;
        $this->cacheEnabled = $cacheEnabled;
        $this->cacheMetaBagFactory = $cacheMetaBagFactory;
        $this->invalidateCacheStrategies = array();
        $this->tagCacheStrategies = array();
    }

    /**
     * @param TagCacheStrategyInterface $tagCacheStrategy
     */
    public function addTagCacheStrategy (TagCacheStrategyInterface $tagCacheStrategy)
    {
        $this->tagCacheStrategies[] = $tagCacheStrategy;
    }

    /**
     * @param InvalidateCacheStrategyInterface $invalidateCacheStrategy
     */
    public function addInvalidateCacheStrategy (InvalidateCacheStrategyInterface $invalidateCacheStrategy)
    {
        $this->invalidateCacheStrategies[] = $invalidateCacheStrategy;
    }

    /**
     * @param Request        $request
     * @param array|string   $cacheTags
     * @param bool           $isCollection
     */
    public function tagController (Request $request, $cacheTags = array(), $isCollection = true)
    {
        $cacheMetaBag = $this->cacheMetaBagFactory->create($request, $cacheTags, $isCollection);
        $this->tag($cacheMetaBag);
    }

    /**
     * @param $entity
     */
    public function invalidateOnInsert($entity)
    {
        $cacheMetaBag = $this->cacheMetaBagFactory->createInsertMetaBag($entity);
        $this->invalidate($cacheMetaBag);
    }

    /**
     * @param $entity
     */
    public function invalidateOnUpdate($entity)
    {
        $cacheMetaBag = $this->cacheMetaBagFactory->createUpdateMetaBag($entity);
        $this->invalidate($cacheMetaBag);
    }

    /**
     * @param $entity
     */
    public function invalidateOnDelete($entity)
    {
        $cacheMetaBag = $this->cacheMetaBagFactory->createDeleteMetaBag($entity);
        $this->invalidate($cacheMetaBag);
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     */
    public function tag (CacheMetaBag $cacheMetaBag)
    {
        /** @var TagCacheStrategyInterface $tagCacheStrategy */
        foreach ($this->tagCacheStrategies as $tagCacheStrategy) {
            if ($tagCacheStrategy->canTag($cacheMetaBag)) {
                $tags = $tagCacheStrategy->getTags($cacheMetaBag);
                if (is_array($tags)) {
                    $this->addTags($tags);
                }
            }
        }
    }

    /**
     * @param CacheMetaBag $cacheMetaBag
     */
    public function invalidate (CacheMetaBag $cacheMetaBag)
    {
        /** @var InvalidateCacheStrategyInterface $invalidateCacheStrategy */
        foreach ($this->invalidateCacheStrategies as $invalidateCacheStrategy) {
            if ($invalidateCacheStrategy->canInvalidate($cacheMetaBag)) {
                $tags = $invalidateCacheStrategy->getTags($cacheMetaBag);
                if (is_array($tags)) {
                    $this->invalidateTags($tags);
                }
            }
        }
    }

    /**
     * @param array $tags
     */
    public function addTags($tags)
    {
        if (!$this->cacheEnabled) {
            return;
        }

        $this->tagHandler->addTags($tags);
    }

    /**
     * @param array $tags
     */
    public function invalidateTags($tags)
    {
        if (!$this->cacheEnabled) {
            return;
        }

        $this->tagHandler->invalidateTags($tags);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getCacheTagsForMixedReviewsAndWishes(Request $request)
    {
        if ($request->query->get('withWish')) {
            $cacheTags = array(CacheTag::WISH, CacheTag::REVIEW);
        } else if ($request->query->get('wish')) {
            $cacheTags = CacheTag::WISH;
        } else {
            $cacheTags = CacheTag::REVIEW;
        }

        return $cacheTags;
    }

}