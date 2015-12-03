<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Aws\S3Client;
use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Entity\Cuisine;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Fixtures\YamlFixturesLoader;
use Tastd\Bundle\CoreBundle\Google\GeocoderClient;
use Tastd\Bundle\CoreBundle\Google\GooglePlaceClient;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Manager\AddressManager;
use Tastd\Bundle\CoreBundle\Manager\RestaurantManager;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;

/**
 * Class RestaurantImportCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class RestaurantImportCommand extends ContainerAwareCommand
{
    /** @var YamlFixturesLoader */
    protected $yamlFixturesLoader;

    /** @var Registry */
    protected $doctrine;

    /** @var GooglePlaceClient */
    protected $googlePlaceClient;

    /** @var RestaurantManager  */
    protected $restaurantManager;

    /** @var EntityManager  */
    protected $entityManager;

    /** @var CuisineRepository */
    protected $cuisineRepository;

    /** @var Validator */
    protected $validator;

    /** @var GeocoderClient */
    protected $geocoder;

    /** @var S3Client $s3 */
    protected $s3;

    /** @var AddressManager  */
    protected $addressManager;

    /**
     * getDependencies
     */
    protected function getDependencies()
    {
        $container = $this->getContainer();
        $this->yamlFixturesLoader = $container->get('tastd.yaml_fixtures_loader');
        /** @var Registry */
        $this->doctrine = $container->get('doctrine');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->cuisineRepository = $this->entityManager->getRepository(Cuisine::SHORTCUT_CLASS_NAME);
        $this->googlePlaceClient = $container->get('tastd.google_place_client');
        $this->restaurantManager = $container->get('tastd.restaurant_manager');
        $this->addressManager = $container->get('tastd.address_manager');
        $this->validator = $container->get('validator');
        $this->geocoder = $container->get('tastd.geocoder');
        $this->s3 = $container->get('tastd.s3_client');

    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:import:restaurants')
            ->setDescription('Import restaurants from google place')
            ->addOption('city', 'c', InputOption::VALUE_OPTIONAL, 'City address')
            ->addOption('latitude', 'lat', InputOption::VALUE_OPTIONAL, 'Latitude')
            ->addOption('radius', 'r', InputOption::VALUE_OPTIONAL, 'Radius. Max 50000', 1000)
            ->addOption('longitude', 'lng', InputOption::VALUE_OPTIONAL, 'Longitude');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatOutput($output);
        $this->getDependencies();
        $latitude = $input->getOption('latitude');
        $longitude = $input->getOption('longitude');
        $radius = $input->getOption('radius');
        $city = $input->getOption('city');

        if (!$city && !($latitude && $longitude)) {
            return $output->writeln('<error>Insert city parameter or latitude + longitude.</error>');
        }

        if ($city) {
            $geocodedAddress = $this->geocoder->geocode($city);
            $latitude = $geocodedAddress->getLat();
            $longitude = $geocodedAddress->getLng();
        }

        $restaurants = array();
        $nextPage = null;

        do {
            $output->writeln('Searching in google place');
            $placeResults = $this->googlePlaceClient->search($latitude, $longitude, null, $radius, $nextPage);
            $nextPage = $placeResults->getNextPage();
            foreach ($placeResults->toArray() as $placeResult) {
                $restaurant = new Restaurant();
                $fullPlaceResult = $this->googlePlaceClient->detail($placeResult->getId());
                $restaurant->setName($placeResult->getName());
                $restaurant->setLat($fullPlaceResult->getLatitude());
                $restaurant->setLng($fullPlaceResult->getLongitude());
                $restaurant->setAddress($fullPlaceResult->getFormattedAddress());

                $address = new Address();
                $address->setCity($fullPlaceResult->getCity());
                $address->setLat($fullPlaceResult->getLatitude());
                $address->setLng($fullPlaceResult->getLongitude());
                $address->setFormattedAddress($fullPlaceResult->getFormattedAddress());
                $this->addressManager->hydrateGeoname($address);
                $restaurant->setGeoname($address->getGeoname());


                $photosReferences = $fullPlaceResult->getPhotoReferences();
                if (count($photosReferences)>0) {
                    $message = $this->googlePlaceClient->image($photosReferences[0]);
                    $imageContent = $message->getContent();
                    $filename = $this->s3->uploadData($imageContent, 'restaurant/');
                    $restaurant->setPicture($filename);
                }

                $errors = $this->validator->validate($restaurant);
                if (count($errors) > 0) {
                    $output->writeln(sprintf('<error>Import error:</error> %s ', (string) $errors));
                } else {
                    $restaurants[] = $restaurant;
                    $this->entityManager->persist($restaurant);
                    $output->writeln(sprintf('Imported: <info>%s</info> - %s', $restaurant->getName(), $address->getFormattedAddress()));
                }
            }
        } while ($nextPage);

        $this->entityManager->flush();

        $output->writeln(sprintf('<success>Imported successfully %d Restaurants</success>', count($restaurants)));

    }

    /**
     * @param OutputInterface $output
     */
    public function formatOutput(OutputInterface $output)
    {
        // Colors: black, red, green, yellow, blue, magenta, cyan and white.
        // Options: bold, underscore, blink, reverse and conceal.

        $style = new OutputFormatterStyle('white', 'green', array('bold'));
        $output->getFormatter()->setStyle('success', $style);
    }

}