<?php

namespace Tastd\Bundle\CoreBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Review
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Review
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Review';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Review';

    /** @var int */
    protected $id;
    /** @var User */
    protected $user;
    /** @var Restaurant */
    protected $restaurant;
    /** @var Cuisine */
    protected $cuisine;
    /** @var Geoname */
    protected $geoname;

    /** @var int */
    protected $cost;
    /** @var int */
    protected $drinkCost;
    /** @var int */
    protected $dollarCost;
    /** @var int */
    protected $dollarDrinkCost;

    /** @var string */
    protected $comment;
    /** @var int */
    protected $score;
    /** @var int */
    protected $position;
    /** @var \DateTime  */
    protected $created;
    /** @var \DateTime  */
    protected $updated;
    /** @var ArrayCollection */
    protected $tags;
    /** @var array */
    protected $photos;
    /** @var ArrayCollection */
    protected $taggedFriends;

    /** @var \DateTime  */
    protected $lastVisited;
    /** @var integer  */
    protected $visitCount;
    /** @var string  */
    protected $mustHave;
    /** @var string  */
    protected $dishes;
    /** @var string  */
    protected $place;
    /** @var string  */
    protected $dressCode;
    /** @var string  */
    protected $discoveredOn;







    /**
     * __construct
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->tags = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->taggedFriends = new ArrayCollection();
        $this->visitCount = 0;
        $this->lastVisited = new \DateTime();
        $this->score = 0;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->restaurant . ' - ' . $this->position;
    }

    /**
     * Set cost
     * @param integer $cost
     *
     * @return Review
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return integer 
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set comment
     * @param string $comment
     *
     * @return Review
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set created
     * @param \DateTime $created
     *
     * @return Review
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     * @param \DateTime $updated
     *
     * @return Review
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set restaurant
     * @param Restaurant $restaurant
     *
     * @return Review
     */
    public function setRestaurant(Restaurant $restaurant = null)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }


    /**
     * onPreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);

        return $this;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
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
     * @return Cuisine
     */
    public function getCuisine()
    {
        return $this->cuisine;
    }

    /**
     * @param Cuisine $cuisine
     */
    public function setCuisine($cuisine)
    {
        $this->cuisine = $cuisine;
    }

    /**
     * @return Geoname
     */
    public function getGeoname()
    {
        return $this->geoname;
    }

    /**
     * @param Geoname $geoname
     */
    public function setGeoname($geoname)
    {
        $this->geoname = $geoname;
    }

    /**
     * array
     */
    public function getTagNames()
    {
        $tagNames = array();
        foreach ($this->tags as $tag) {
            $tagNames[] = $tag->getName();
        }
        return $tagNames;
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param mixed $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * @param $taggedFriends
     */
    public function setTaggedFriends($taggedFriends)
    {
        $this->taggedFriends = $taggedFriends;
    }

    /**
     * @return ArrayCollection
     */
    public function getTaggedFriends()
    {
        return $this->taggedFriends;
    }

    /**
     * @param $taggedFriend
     */
    public function addTaggedFriend($taggedFriend)
    {
        $this->taggedFriends->add($taggedFriend);
    }

    /**
     * @return int
     */
    public function getDrinkCost()
    {
        return $this->drinkCost;
    }

    /**
     * @param int $drinkCost
     */
    public function setDrinkCost($drinkCost)
    {
        $this->drinkCost = $drinkCost;
    }

    /**
     * @return int
     */
    public function getDollarCost()
    {
        return $this->dollarCost;
    }

    /**
     * @param int $dollarCost
     */
    public function setDollarCost($dollarCost)
    {
        $this->dollarCost = $dollarCost;
    }

    /**
     * @return int
     */
    public function getDollarDrinkCost()
    {
        return $this->dollarDrinkCost;
    }

    /**
     * @param int $dollarDrinkCost
     */
    public function setDollarDrinkCost($dollarDrinkCost)
    {
        $this->dollarDrinkCost = $dollarDrinkCost;
    }

    /**
     * @return \DateTime
     */
    public function getLastVisited()
    {
        return $this->lastVisited;
    }

    /**
     * @param \DateTime $lastVisited
     */
    public function setLastVisited($lastVisited)
    {
        $this->lastVisited = $lastVisited;
    }

    /**
     * @return int
     */
    public function getVisitCount()
    {
        return $this->visitCount;
    }

    /**
     * @param int $visitCount
     */
    public function setVisitCount($visitCount)
    {
        $this->visitCount = $visitCount;
    }

    /**
     * @return string
     */
    public function getMustHave()
    {
        return $this->mustHave;
    }

    /**
     * @param string $mustHave
     */
    public function setMustHave($mustHave)
    {
        $this->mustHave = $mustHave;
    }

    /**
     * @return string
     */
    public function getDishes()
    {
        return $this->dishes;
    }

    /**
     * @param string $dishes
     */
    public function setDishes($dishes)
    {
        $this->dishes = $dishes;
    }

    /**
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param string $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * @return string
     */
    public function getDressCode()
    {
        return $this->dressCode;
    }

    /**
     * @param string $dressCode
     */
    public function setDressCode($dressCode)
    {
        $this->dressCode = $dressCode;
    }

    /**
     * @return string
     */
    public function getDiscoveredOn()
    {
        return $this->discoveredOn;
    }

    /**
     * @param string $discoveredOn
     */
    public function setDiscoveredOn($discoveredOn)
    {
        $this->discoveredOn = $discoveredOn;
    }








}
