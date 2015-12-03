<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Entity\Factory\RandomRestaurantFactory;
use Tastd\Bundle\CoreBundle\Entity\Factory\RandomReviewFactory;

/**
 * Class ReviewGeneratorCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class ReviewGeneratorCommand extends Command
{

    protected $randomRestaurantFactory;

    /**
     * @param RandomReviewFactory $randomReviewFactory
     */
    public function __construct(RandomReviewFactory $randomReviewFactory)
    {
        parent::__construct();
        $this->randomReviewFactory = $randomReviewFactory;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:generate:reviews')
            ->setDescription('Generate random reviews data')
            ->addOption('number', null, InputOption::VALUE_OPTIONAL, 'Number of reviews', 100);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatOutput($output);
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Do you really want generate random reviews (y/F)? ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $number = $input->getOption('number');
        $this->randomReviewFactory->createAll($number);
        $output->writeln('<success>Reviews Created</success>');
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