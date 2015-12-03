<?php

namespace Tastd\Bundle\CoreBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class Tag
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Tag
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Tag';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Tag';

    /** @var integer  */
    protected $id;
    /** @var string  */
    protected $name;
    /** @var integer  */
    protected $count;
    /** @var boolean */
    protected $highlight;
    /** @var Review[]  */
    protected $reviews;
    /** @var integer */
    protected $groupId;
    /** @var string */
    protected $groupName;

    /**
     * __count
     */
    public function __construct()
    {
        $this->count = 0;
        $this->highlight = false;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * incrementCount
     */
    public function incrementCount()
    {
        $this->count ++;
    }

    /**
     * @return boolean
     */
    public function isHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param boolean $highlight
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }

    /**
     * @return Review[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param Review[] $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }




}