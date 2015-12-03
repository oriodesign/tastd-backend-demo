<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class GeonameFindCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class GeonameFindCommand extends Command
{

    protected $geonameRepository;

    /**
     * @param GeonameRepository $geonameRepository
     */
    public function __construct(GeonameRepository $geonameRepository)
    {
        parent::__construct();
        $this->geonameRepository = $geonameRepository;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:geoname:find')
            ->setDescription('Find geoname')
            ->addArgument('near', InputArgument::REQUIRED, 'Near which city?')
            ->addArgument('country', InputArgument::OPTIONAL, 'Country??');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $near = $input->getArgument('near');
        $country = $input->getArgument('country');
        $parameters = array('asciiName' => $near );
        if ($country) {
            $parameters['country'] = $country;
        }

        $targetGeoname = $this->geonameRepository->findOneBy($parameters);

        if (!$targetGeoname) {
            return $output->writeln('<error>No geoname found</error>');
        }

        $results = $this->geonameRepository->getNearbyGeonames($targetGeoname);

        foreach ($results as $geoname) {
            $message = sprintf('[%s] %s (pop: %s)', $geoname->getId(), $geoname->getAsciiName(), $geoname->getPopulation());
            if ($this->geonameRepository->checkIfUsed($geoname)) {
                $message = '<error>' . $message . ' ** USED ** </error>';
            }
            /** @var Geoname $geoname */
            $output->writeln($message);
        }

    }


}