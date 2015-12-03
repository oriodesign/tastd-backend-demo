<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tastd\Bundle\CoreBundle\Command\Output\Printer;;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Foursquare\FoursquareClient;
use Tastd\Bundle\CoreBundle\Foursquare\VenueManager;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class FoursquareImportCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class FoursquareImportCommand extends Command
{
    /** @var GeonameRepository */
    protected $geonameRepository;
    /** @var FoursquareClient  */
    protected $foursquareClient;
    /** @var VenueManager */
    protected $venueManager;
    /** @var InputInterface */
    protected $input;
    /** @var  OutputInterface */
    protected $output;
    /** @var Printer */
    protected $printer;

    /**
     * @param FoursquareClient   $foursquareClient
     * @param VenueManager       $venueManager
     * @param ValidatorInterface $validator
     * @param EntityManager      $entityManager
     * @param Printer            $printer
     * @param GeonameRepository  $geonameRepository
     */
    public function __construct(
        FoursquareClient $foursquareClient,
        VenueManager $venueManager,
        ValidatorInterface $validator,
        EntityManager $entityManager,
        Printer $printer,
        GeonameRepository $geonameRepository)
    {
        parent::__construct();
        $this->foursquareClient = $foursquareClient;
        $this->venueManager = $venueManager;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->printer = $printer;
        $this->geonameRepository = $geonameRepository;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:foursquare:import')
            ->setDescription('Import from google place top foursquare restaurants')
            ->addOption('city', 'c', InputOption::VALUE_REQUIRED, 'Foursquare city format')
            ->addOption('geoname', 'g', InputOption::VALUE_REQUIRED, 'Tastd geoname id')
            ->addOption('preview', 'p', InputOption::VALUE_NONE, 'Do not persist')
            ->addOption('previewGeoname', null, InputOption::VALUE_NONE, 'Only print geonames')
            ->addOption('usTop', null, InputOption::VALUE_NONE, 'Import US top Top cities')
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'Geoname offset', 0)
            ->addOption('euroTop', null, InputOption::VALUE_NONE, 'Import EURO top Top cities')
            ->addOption('geonamesCount', null, InputOption::VALUE_OPTIONAL, 'Import EURO top Top cities', 100)
            ->addOption('pages', null, InputOption::VALUE_OPTIONAL, 'How many pages of 50 results',1);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->printer->setOutput($output);
        $city = $input->getOption('city');
        $euroTop = $input->getOption('euroTop');
        $usTop = $input->getOption('usTop');
        $geonamesCount = $input->getOption('geonamesCount');

        $geonames = array();

        if ($city) {
            $geonames[] = $this->entityManager->find(Geoname::CLASS_NAME, $city);
        }

        if ($euroTop) {
            $geonames = $this->geonameRepository->findEuroTop($geonamesCount);
        }

        if ($usTop) {
            $geonames = $this->geonameRepository->findUsTop($geonamesCount);
        }

        $geonames = $this->applyOffset($geonames);

        foreach ($geonames as $geoname) {
            $this->printer->writelnEntityShort($geoname);
            $this->importCity($geoname);
        }
    }

    /**
     * @param $geonames
     * @return mixed
     */
    protected function applyOffset($geonames)
    {
        $offset = $this->input->getOption('offset');

        return array_slice($geonames, $offset);
    }

    protected function importCity(Geoname $geoname)
    {
        $previewGeoname = $this->input->getOption('previewGeoname');

        if ($previewGeoname) {
            return;
        }

        if ($geoname->getCountry() === 'US') {
            $cityName = $geoname->getAsciiName() . ', ' . $geoname->getAdmin1();
        } else {
            $cityName = $geoname->getAsciiName() . ',' . $geoname->getCountry();
        }

        $maxPages = $this->input->getOption('pages');
        $currentPage = 0;
        while ($currentPage < $maxPages) {
            $venues = $this->foursquareClient->explore($cityName, $currentPage * 50);
            $this->processResults($venues, $geoname);
            $currentPage++;
        }
    }

    /**
     * @param $venues
     * @param Geoname $geoname
     */
    protected function processResults($venues, Geoname $geoname)
    {
        foreach ($venues as $venue) {
            $this->processVenue($venue, $geoname);
        }
        $this->printer->newLine();
    }

    /**
     * @param $venue
     * @param Geoname $geoname
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function processVenue($venue, Geoname $geoname)
    {
        $preview = $this->input->getOption('preview');
        $restaurant = $this->venueManager->getRestaurant($venue);
        $restaurant->setGeoname($geoname);
        $errors = $this->validator->validate($restaurant);

        if (count($errors) !== 0) {
            $errorString = (string)$errors;
            $this->output->writeln(sprintf('<error>%s</error>',$errorString));
            return;
        }

        $this->printer->writelnEntity($restaurant);

        if ($preview) {
            return;
        }

        $this->entityManager->persist($restaurant);
        $this->entityManager->flush();
    }
}