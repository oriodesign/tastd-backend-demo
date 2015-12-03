<?php

namespace Tastd\Bundle\CoreBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class Restaurant
 *
 * @package Tastd\Bundle\CoreBundle\Entity
 */
class Restaurant
{
    const SHORTCUT_CLASS_NAME = 'TastdCoreBundle:Restaurant';
    const CLASS_NAME = 'Tastd\\Bundle\\CoreBundle\\Entity\\Restaurant';

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $address;
    /** @var string */
    protected $website;
    /** @var string */
    protected $phone;
    /** @var string */
    protected $chef;
    /** @var string */
    protected $instagram;
    /** @var string */
    protected $awards;
    /** @var ArrayCollection */
    protected $reviews;
    /** @var \DateTime */
    protected $created;
    /** @var \DateTime */
    protected $updated;
    /** @var string */
    protected $picture;
    /** @var string */
    protected $thumb;
    /** @var ArrayCollection */
    protected $photos;
    /** @var Cuisine */
    protected $cuisine;
    /** @var array */
    protected $tags;
    /** @var int */
    protected $averageCost;
    /** @var string */
    protected $uploadedPicture;
    /** @var string */
    protected $lat;
    /** @var string */
    protected $lng;
    /** @var Geoname */
    protected $geoname;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Set name
     * @param string $name
     *
     * @return Restaurant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created
     * @param \DateTime $created
     *
     * @return Restaurant
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
     * @return Restaurant
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
     * Set address
     * @param string $address
     *
     * @return Restaurant
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add reviews
     * @param Review $review
     *
     * @return Restaurant
     */
    public function addReview(Review $review)
    {
        $this->reviews[] = $review;
        $review->setRestaurant($this);

        return $this;
    }

    /**
     * @param array $reviews
     */
    public function setReviews($reviews)
    {
        /** @var Review $review */
        foreach ($reviews as $review) {
            $review->setRestaurant($this);
        }
        $this->reviews = $reviews;
    }

    /**
     * Remove reviews
     *
     * @param Review $review
     */
    public function removeReview(Review $review)
    {
        $review->setRestaurant(null);
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }


    /**
     * onPreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @return mixed
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param mixed $thumb
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @param mixed $uploadedPicture
     */
    public function setUploadedPicture($uploadedPicture)
    {
        $this->uploadedPicture = $uploadedPicture;
    }

    /**
     * @return mixed
     */
    public function getUploadedPicture()
    {
        return $this->uploadedPicture;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param string $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
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
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return int
     */
    public function getAverageCost()
    {
        return $this->averageCost;
    }

    /**
     * @param int $averageCost
     */
    public function setAverageCost($averageCost)
    {
        $this->averageCost = $averageCost;
    }

    /**
     * @return string
     */
    public function getRankingKey()
    {
        return $this->geoname->getId() . '-' . $this->cuisine->getId();
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param $photos
     */
    public function setPhotos($photos)
    {
        /** @var Photo $photo */
        foreach ($photos as $photo) {
            $photo->setRestaurant($this);
        }
        $this->$photos = $photos;
    }

    /**
     * Add photo
     * @param Photo $photo
     *
     * @return Restaurant
     */
    public function addPhoto(Photo $photo)
    {
        $this->reviews[] = $photo;
        $photo->setRestaurant($this);

        return $this;
    }

    /**
     * @return string
     */
    public function getChef()
    {
        return $this->chef;
    }

    /**
     * @param string $chef
     */
    public function setChef($chef)
    {
        $this->chef = $chef;
    }

    /**
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * @param string $instagram
     */
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;
    }

    /**
     * @return string
     */
    public function getAwards()
    {
        return $this->awards;
    }

    /**
     * @param string $awards
     */
    public function setAwards($awards)
    {
        $this->awards = $awards;
    }

}
