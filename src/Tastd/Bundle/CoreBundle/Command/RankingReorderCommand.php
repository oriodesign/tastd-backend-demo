<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class RankingReorderCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class RankingReorderCommand extends Command
{

    protected $reviewRepository;
    /** @var OutputInterface  */
    protected $output;
    protected $errorCount;
    protected $rightCount;
    protected $preview;
    protected $input;

    /**
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(
        ReviewRepository $reviewRepository)
    {
        parent::__construct();
        $this->reviewRepository = $reviewRepository;
        $this->errorCount = 0;
        $this->rightCount = 0;
        $this->preview = false;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:ranking:reorder')
            ->setDescription('Fix reordering issues')
            ->addOption('preview', null, InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;
        $this->preview = $this->input->getOption('preview');
        $this->output->writeln('Retrieve all rankings');
        $data = $this->reviewRepository->getAllRankingsData();
        foreach ($data as $row) {
            $this->handleRankingData($row);
        }

        $this->output->writeln('');
        $this->output->writeln('============================');
        $this->output->writeln(sprintf(
            'Reordering completed with %s correct lists and %s errors',
            $this->rightCount,
            $this->errorCount
        ));
    }

    protected function handleRankingData($data)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln('');
            $this->output->writeln(sprintf(
                'Handle ranking <fg=blue>[user:%s geo:%s cuisine:%s]</>',
                $data['user'],
                $data['geoname'],
                $data['cuisine']
            ));
        }
        $reviews = $this->reviewRepository
            ->getSingleRankingPositionData($data['user'], $data['geoname'], $data['cuisine']);
        try {
            $this->checkRankingData($reviews);
        } catch (\Exception $e) {
            $this->fixPositioning($reviews);
        }
    }

    /**
     * @param $reviews
     */
    protected function fixPositioning($reviews)
    {
        $this->output->writeln('Fix positioning.');
        if ($this->preview) {
            return;
        }
        $rightPosition = 1;
        foreach ($reviews as $review) {
            $this->output->writeln(sprintf('Update review %s with position %s.', $review['id'], $rightPosition));

            $this->reviewRepository->updatePosition($review['id'], $rightPosition);
            $rightPosition++;
        }
    }

    /**
     * @param array $reviews
     *
     * @throws \Exception
     */
    protected function checkRankingData($reviews)
    {
        $rightPosition = 1;
        foreach ($reviews as $review) {
            $position = (int) $review['position'];
            if ($rightPosition !== $position) {
                $this->errorCount ++;
                $this->output->writeln(
                    sprintf('<error>Something is messed up here! %s != %s</error>',
                        $review['position'],
                        $rightPosition
                    ));
                throw new \Exception('Positioning is messed up');
            }
            $rightPosition++;
        }
        $this->rightCount ++;
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln('<info>Everything is all right here.</info>');
        }
    }



}