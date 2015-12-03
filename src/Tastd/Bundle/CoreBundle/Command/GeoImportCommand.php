<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class GeoImportCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class GeoImportCommand extends ContainerAwareCommand
{

    /** @var Registry */
    protected $doctrine;
    /** @var EntityManager */
    protected $entityManager;
    /** @var GeonameRepository */
    protected $geonameRepository;
    /** @var string */
    protected $assetDir;
    /** @var array */
    protected $fileNames;
    protected $dbhost;
    protected $dbuser;
    protected $dbpwd;
    protected $dbname;

    /**
     * getDependencies
     */
    protected function getDependencies()
    {
        $container = $this->getContainer();
        /** @var Registry */
        $this->doctrine = $container->get('doctrine');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->geonameRepository = $this->entityManager->getRepository(Geoname::CLASS_NAME);
        $this->assetDir = $container->get('kernel')->getRootDir() . '/../assets/';
        $this->fileNames = array('geonames.sql', 'geo_translations.sql');

        $this->dbhost = $container->getParameter('database_host');
        $this->dbname = $container->getParameter('database_name');
        $this->dbuser = $container->getParameter('database_user');
        $this->dbpwd = $container->getParameter('database_password');
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:geo:import')
            ->setDescription('Import geo data from assets')
            ->addOption('truncate', null, InputOption::VALUE_OPTIONAL)
            ->addOption('currency', null, InputOption::VALUE_OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getDependencies();
        $this->disableForeignChecks();

        if ($input->getOption('truncate')) {
            $output->writeln('Delete all geonames and related entities');
            $this->removeAllGeonames();
            $output->writeln('All geonames deleted');
        }

        foreach ($this->fileNames as $filename) {
            $this->extractFile($output, $filename);
            $this->importSQL($output, $filename);
            $this->removeFile($output, $filename);
        }

        if ($input->getOption('currency')) {
            $statement = $this->entityManager->getConnection()->prepare('UPDATE geonames g, countries c SET g.currency_symbol = c.currency_symbol WHERE g.country = c.iso_alpha2');
            $statement->exec();
        }

        $this->enableForeignChecks();

        $output->writeln(sprintf('Imported %s files: ', count($this->fileNames)));
    }

    protected function removeAllGeonames()
    {
        $geonames = $this->entityManager->getRepository(Geoname::CLASS_NAME)->findAll();
        foreach ($geonames as $geoname) {
            $this->entityManager->remove($geoname);
        }
        $this->entityManager->flush();
    }

    protected function importSQL(OutputInterface $output, $filename)
    {
        $output->writeln('Reading SQL: ' . $filename);
        $command = sprintf("mysql -h %s -u %s -p%s %s < %s", $this->dbhost, $this->dbuser, $this->dbpwd, $this->dbname, $this->assetDir . $filename );
        shell_exec($command);
        $output->writeln('Executed: ' . $filename);
    }

    protected function extractFile(OutputInterface $output, $filename)
    {
        $zip = new \ZipArchive();
        $res = $zip->open($this->assetDir . $filename . '.zip');
        if ($res === true) {
            $zip->extractTo($this->assetDir);
            $zip->close();
            $output->writeln('Unzipped ' . $filename);
        } else {
            $output->writeln('Error during unzip');
        }
    }

    protected function removeFile(OutputInterface $output, $filename)
    {
        unlink($this->assetDir . $filename);
        $output->writeln('Delete temporary file: ' . $filename);
    }


    protected function disableForeignChecks()
    {
        $sql = 'SET FOREIGN_KEY_CHECKS = 0;';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
    }

    protected function enableForeignChecks()
    {
        $sql = 'SET FOREIGN_KEY_CHECKS = 1;';
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
    }


}