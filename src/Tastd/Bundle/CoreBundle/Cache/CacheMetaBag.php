<?php

namespace Tastd\Bundle\CoreBundle\Cache;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class CacheMetaBag
 *
 * @package Tastd\Bundle\CoreBundle\Cache
 */
class CacheMetaBag
{
    /** @var Request */
    protected $request;
    /** @var array */
    protected $cacheTags;
    /** @var boolean */
    protected $isCollection;
    /** @var mixed  */
    protected $entity;

    /**
     * __construct
     */
    public function __construct ()
    {
        $this->cacheTags = array();
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getCacheTags()
    {
        return $this->cacheTags;
    }

    /**
     * @param boolean $isCollection
     */
    public function isCollection($isCollection)
    {
        $this->isCollection = $isCollection;
    }

    /**
     * @param string|array $tags
     */
    public function setCacheTags($tags)
    {
        $this->cacheTags = is_array($tags) ?
            $tags : array($tags);
    }

    /**
     * @param string $tag
     *
     * @return boolean
     */
    public function matchTags($tag)
    {
        return in_array($tag, $this->cacheTags);
    }

    /**
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        $parameter = $this->request->query->get($parameterName);

        return (null !== $parameter);
    }

    /**
     * @param string $parameterName
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        return $this->request->query->get($parameterName);
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }



}