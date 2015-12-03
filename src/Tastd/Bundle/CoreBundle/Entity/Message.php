<?php

namespace Tastd\Bundle\CoreBundle\Entity;
use Tastd\Bundle\CoreBundle\Key\MessageStatus;

/**
 * Class Message
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Message
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Message';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Message';

    /** @var integer */
    protected $id;
    /** @var string */
    protected $category;
    /** @var string */
    protected $content;
    /** @var string */
    protected $title;
    /** @var User */
    protected $user;
    /** @var \DateTime */
    protected $created;
    /** @var \DateTime */
    protected $updated;
    /** @var string */
    protected $status;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->status = MessageStatus::PENDING;
    }

    public function __toString()
    {
        return (string)$this->title;
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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