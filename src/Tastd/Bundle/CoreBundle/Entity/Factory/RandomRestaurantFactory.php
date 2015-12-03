<?php

namespace Tastd\Bundle\CoreBundle\Entity\Factory;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;
use Tastd\Bundle\CoreBundle\Entity\Geoname;


/**
 * Class RandomRestaurantFactory
 *
 * @package Tastd\Bundle\CoreBundle\Entity\Factory
 */
class RandomRestaurantFactory
{
    protected $entityManager;
    protected $cuisineRepository;
    protected $geonameRepository;
    protected $cuisines;
    protected $geonames;
    protected $names;
    protected $suffixes;
    protected $streetNames;

    /**
     * @param EntityManager $entityManager
     * @param CuisineRepository $cuisineRepository
     * @param GeonameRepository $geonameRepository
     */
    public function __construct(
        EntityManager $entityManager,
        CuisineRepository $cuisineRepository,
        GeonameRepository $geonameRepository)
    {
        $this->entityManager = $entityManager;
        $this->cuisineRepository = $cuisineRepository;
        $this->geonameRepository = $geonameRepository;
        $this->names = array(
            'Pizza',
            'Kebab',
            'Restaurant',
            'Chef',
            'Sandwich',
            'Club',
            'Eat',
            'Cook',
            'Veggie',
            'Meat',
            'Pub',
            'Cafe',
            'Trattoria'
        );
        $this->suffixes = array(
            'Super',
            'Yummy',
            'Tasty',
            'Gino',
            'Luigi',
            'Roma',
            'Milano',
            'London',
            'Extreme',
            'John',
            'Deluxe',
            'Fish',
            'Flash',
            'Healthy'
        );
        $this->streetNames = array(
            'Old Street',
            'New Road',
            'Great Avenue',
            'Large Square',
            'Winchester Square',
            'London Bridge',
            'Leicester Square',
            'Victoria Square'
        );
    }

    /**
     * @param int $number
     */
    public function createAll($number = 100)
    {
        for ($i = 0; $i < $number; $i++) {
            $restaurant = $this->create();
            $this->entityManager->persist($restaurant);
        }
        $this->entityManager->flush();
    }

    public function create()
    {
        $geoname = $this->getRandomGeoname();
        $restaurant = new Restaurant();
        $restaurant->setWebsite('http://www.google.com');
        $restaurant->setAddress($this->getRandomAddress());
        $restaurant->setLat($this->getRandomLat($geoname));
        $restaurant->setLng($this->getRandomLng($geoname));
        $restaurant->setAverageCost(rand(0,200));
        $restaurant->setName($this->getRandomName());
        $restaurant->setCuisine($this->getRandomCuisine());
        $restaurant->setGeoname($geoname);
        $restaurant->setPhone('04422556677');
        $restaurant->setPicture(null);
        $restaurant->setThumb(null);

        return $restaurant;
    }

    /**
     * @param Geoname $geoname
     * @return float
     */
    public function getRandomLat(Geoname $geoname)
    {
        $lat = floatval($geoname->getLat());

        return $lat + $this->getRandomOffset();
    }

    /**
     * @param Geoname $geoname
     * @return float
     */
    public function getRandomLng(Geoname $geoname)
    {
        $lng = floatval($geoname->getLng());

        return $lng + $this->getRandomOffset();
    }

    /**
     * @return string
     */
    public function getRandomAddress()
    {
        return rand(1,300) . $this->getRandomStreetName();
    }

    /**
     * @return string
     */
    public function getRandomName()
    {
        return $this->names[array_rand($this->names)] . ' ' . $this->suffixes[array_rand($this->suffixes)];
    }

    /**
     * @return mixed
     */
    public function getRandomStreetName()
    {
        return $this->streetNames[array_rand($this->streetNames)];
    }

    /**
     * @return mixed
     */
    public function getRandomGeoname()
    {
        if (!$this->geonames) {
            $this->geonames = $this->geonameRepository->findAll();
        }

        return $this->geonames[array_rand($this->geonames)];
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
     * @return float
     */
    private function getRandomOffset()
    {
        if (rand(0,1)) {
            return rand(0,1000) / 100000;
        }
        return -1 * rand(0,1000) / 100000;
    }

}