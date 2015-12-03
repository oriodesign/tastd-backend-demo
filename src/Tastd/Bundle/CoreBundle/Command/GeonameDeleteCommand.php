<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class GeonameDeleteCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class GeonameDeleteCommand extends Command
{

    protected $geonameRepository;
    protected $geonamesToBeRemoved;

    /**
     * @param GeonameRepository $geonameRepository
     * @param EntityManager     $entityManager
     */
    public function __construct(GeonameRepository $geonameRepository, EntityManager $entityManager)
    {
        parent::__construct();
        $this->geonameRepository = $geonameRepository;
        $this->entityManager = $entityManager;
        $this->geonamesToBeRemoved = array();
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:geoname:delete')
            ->setDescription('Delete geonames')
            ->addArgument('ids', InputArgument::REQUIRED, 'String of comma separated ids');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $argumentIds = $input->getArgument('ids');
        $ids = explode(',', $argumentIds);
        foreach ($ids as $id) {
            $output->writeln($this->checkId($id));
        }
        $question = new ConfirmationQuestion('Do you really want to delete these unused geonames (y/F)? ', false);
        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        foreach ($this->geonamesToBeRemoved as $geoname) {
            $this->entityManager->remove($geoname);
        }
        $this->entityManager->flush();
    }

    /**
     * @param int $id
     *
     * @return string
     */
    protected function checkId($id)
    {
        $geoname = $this->geonameRepository->find($id);

        if ($geoname) {
            $removedGeonames[] = $geoname;
            if ($this->geonameRepository->checkIfUsed($geoname)) {
                return sprintf('<error>Remove [USED] [%s] %s</error>', $geoname->getCountry(), $geoname->getAsciiName());
            }
            $this->geonamesToBeRemoved[] = $geoname;

            return sprintf('Remove [%s] %s ', $geoname->getCountry(), $geoname->getAsciiName());
        }

        return 'No geoname with id ' . $id;
    }


}