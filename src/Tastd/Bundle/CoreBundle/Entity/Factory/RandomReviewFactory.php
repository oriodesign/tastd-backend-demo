<?php

namespace Tastd\Bundle\CoreBundle\Entity\Factory;

use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Repository\RestaurantRepository;
use Tastd\Bundle\CoreBundle\Repository\TagRepository;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class RandomReviewFactory
 *
 * @package Tastd\Bundle\CoreBundle\Entity\Factory
 */
class RandomReviewFactory
{

    protected $restaurantRepository;
    protected $entityManager;
    protected $cuisineRepository;
    protected $userRepository;
    protected $tagRepository;
    protected $cuisines;
    protected $restaurants;
    protected $tags;
    protected $users;
    protected $connection;

    /**
     * @param EntityManager $entityManager
     * @param RestaurantRepository $restaurantRepository
     * @param CuisineRepository $cuisineRepository
     * @param TagRepository $tagRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        EntityManager $entityManager,
        RestaurantRepository $restaurantRepository,
        CuisineRepository $cuisineRepository,
        TagRepository $tagRepository,
        UserRepository $userRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->entityManager = $entityManager;
        $this->cuisineRepository = $cuisineRepository;
        $this->tagRepository = $tagRepository;
        $this->userRepository = $userRepository;
        $this->connection = $this->entityManager->getConnection();
    }


    public function createAll()
    {
        $this->users = $this->userRepository->findAll();
        if (!$this->restaurants) {
            $this->restaurants = $this->restaurantRepository->findAll();
        }

        foreach ($this->users as $user) {
            $this->createRandomsReviews($user);
        }
    }

    /**
     * @param User $user
     */
    private function createRandomsReviews(User $user)
    {
        shuffle($this->restaurants);
        $i = 0;
        $max = rand(0, 100);
        /** @var Restaurant $restaurant */
        foreach ($this->restaurants as $restaurant) {
            if ($i > $max) {
                return;
            }
            $tags = $this->getRandomTags();
            $comment = $this->getRandomComment();
            foreach ($tags as $tag) {
                $comment .= ' #'. $tag->getName();
            }

            $this->connection->exec(
                'INSERT INTO reviews (
                  cuisine_id,
                  geoname_id,
                  user_id,
                  restaurant_id,
                  cost,
                  comment,
                  position,
                  score
                  )
                  VALUES
                  (
                  '.$this->getRandomCuisine()->getId().',
                  '.$restaurant->getGeoname()->getId().',
                  '.$user->getId().',
                  '.$restaurant->getId().',
                  '.$this->getRandomCost().',
                  "'.$comment.'",
                  '.$i.',
                  '.$this->getRandomScore().'
                  )');

            $id = $this->connection->lastInsertId();
            foreach ($tags as $tag) {
                $this->connection->exec('INSERT INTO review_tag (review_id, tag_id) VALUES ('.$id.', '.$tag->getId().')');
            }

            $i++;
        }
    }

    /**
     * @return string
     */
    private function getRandomComment()
    {
        return 'Random comment';
    }

    /**
     * @return int
     */
    private function getRandomCost()
    {
        return rand(10,200);
    }

    /**
     * getRandomCuisine
     */
    private function getRandomCuisine()
    {
        if (!$this->cuisines) {
            $this->cuisines = $this->cuisineRepository->findAll();
        }

        return $this->cuisines[array_rand($this->cuisines)];
    }


    /**
     * @return array
     */
    public function getRandomTags()
    {
        if (!$this->tags) {
            $this->tags = $this->tagRepository->findAll();
        }
        $keys = array_rand($this->tags, rand(1,count($this->tags)));
        $tags = array();
        if (is_array($keys)) {
            foreach ($keys as $key){
                $tags[] = $this->tags[$key];
            }
        } else {
            $tags = array($this->tags[$keys]);
        }

        return $tags;
    }

    /**
     * getRandomScore
     */
    private function getRandomScore()
    {
        return rand(0,100);
    }

}