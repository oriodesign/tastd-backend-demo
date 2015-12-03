<?php

namespace Tastd\Bundle\CoreBundle\Entity;

/**
 * Class Post
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Post
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Post';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Post';

    /** @var int */
    protected $id;

    /** @var string */
    protected $title;
    /** @var string */
    protected $content;

    /** @var string */
    protected $thumb;
    /** @var string */
    protected $picture;

    /** @var \DateTime */
    protected $created;
    /** @var \DateTime */
    protected $updated;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * onPreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param string $thumb
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }



}