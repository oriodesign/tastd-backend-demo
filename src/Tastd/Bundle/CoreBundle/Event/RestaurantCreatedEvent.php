<?php

namespace Tastd\Bundle\CoreBundle\Event;

use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\User;

/**
 * Class RestaurantCreatedEvent
 *
 * @package Tastd\Bundle\CoreBundle\Event
 */
class RestaurantCreatedEvent extends ApiEvent
{
    /** @var Restaurant */
    protected $restaurant;
    /** @var User $user */
    protected $user;

    /**
     * @param Restaurant $restaurant
     * @param User       $user
     */
    public function __construct(Restaurant $restaurant, User $user)
    {
        $this->restaurant = $restaurant;
        $this->user = $user;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return array(
            'coordinate' => array(
                $this->restaurant->getLng(),
                $this->restaurant->getLat(),
            )
        );
    }

}