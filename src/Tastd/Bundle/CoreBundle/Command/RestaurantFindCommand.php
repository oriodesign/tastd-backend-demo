<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Repository\RestaurantRepository;

/**
 * Class RestaurantFindCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class RestaurantFindCommand extends Command
{

    protected $restaurantRepository;
    protected $input;
    protected $output;
    protected $question;
    protected $page;
    protected $nbPages;
    protected $pager;

    /**
     * @param RestaurantRepository $restaurantRepository
     */
    public function __construct(RestaurantRepository $restaurantRepository)
    {
        parent::__construct();
        $this->page = 1;
        $this->nbPages = 1;
        $this->restaurantRepository = $restaurantRepository;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:restaurant:find')
            ->setDescription('Find restaurant')
            ->addOption('all', 'a',  InputOption::VALUE_NONE, 'All restaurants');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->question = $this->getHelper('question');
        $this->pager = $this->getPager();
        $this->pager->setMaxPerPage(50);
        $this->nbPages = $this->pager->getNbPages();
        $this->printHeader($output);
        $this->loadMore();
    }

    /**
     * @return bool
     */
    protected function loadMore()
    {
        $this->pager->setCurrentPage($this->page);
        $this->printResults($this->pager->getCurrentPageResults());

        if ($this->page >= $this->nbPages ) {
            $this->output->writeln("No more pages");
            return false;
        }

        $question = new ConfirmationQuestion('Load More? ', false);
        if (!$this->question->ask($this->input, $this->output, $question)) {
            return false;
        }
        $this->page = $this->pager->getNextPage();
        $this->loadMore();
    }

    /**
     * @return Pagerfanta
     */
    protected function getPager()
    {
        return $this->restaurantRepository->getRestaurantsPager(new Request());
    }

    /**
     * printHeader
     */
    protected function printHeader()
    {
        $this->output->writeln(sprintf('[%s] %s | %s   | %s   | %s | %s | %s | %s | %s | %s | %s | %s',
            $this->cell("ID", 4),
            $this->cell("Name"),
            $this->cell("Cuisine"),
            $this->cell("Geoname"),
            $this->cell("address"),
            $this->cell("average cost"),
            $this->cell("lat"),
            $this->cell("lng"),
            $this->cell("website"),
            $this->cell("phone"),
            $this->cell("picture"),
            $this->cell("updated"),
            $this->cell("created")
        ));

        $this->output->writeln('============================================================================================================================================================');

    }

    /**
     * @param $restaurants
     */
    protected function printResults($restaurants)
    {
        /** @var Restaurant $restaurant */
        foreach ($restaurants as $restaurant) {
            $cuisineId = 'null';
            $geonameId = 'null';
            $cuisine = $restaurant->getCuisine();
            $geoname = $restaurant->getGeoname();

            if ($cuisine) {
                $cuisineId = $cuisine->getId();
            }
            if ($geoname) {
                $geonameId = $geoname->getId();
            }

            $this->output->writeln(sprintf('[%s] %s | C:%s | G:%s | %s | %s | %s | %s | %s | %s | %s | %s',
                $this->cell($restaurant->getId(), 4),
                $this->cell($restaurant->getName()),
                $this->cell($cuisineId),
                $this->cell($geonameId),
                $this->cell($restaurant->getAddress()),
                $this->cell($restaurant->getAverageCost()),
                $this->cell($restaurant->getLat()),
                $this->cell($restaurant->getLng()),
                $this->cell($restaurant->getWebsite()),
                $this->cell($restaurant->getPhone()),
                $this->cell($restaurant->getPicture()),
                $this->cell($restaurant->getUpdated()->format(\DateTIme::ISO8601)),
                $this->cell($restaurant->getCreated()->format(\DateTIme::ISO8601))
            ));

        }

    }

    /**
     * @param string $str
     * @param int    $len
     *
     * @return string
     */
    protected function cell($str, $len = 10) {
        return str_pad(substr($str, 0, $len), $len);
    }


}