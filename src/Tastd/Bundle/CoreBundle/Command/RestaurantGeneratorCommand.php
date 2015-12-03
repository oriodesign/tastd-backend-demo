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

/**
 * Class RestaurantGeneratorCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class RestaurantGeneratorCommand extends Command
{

    protected $randomRestaurantFactory;

    /**
     * @param RandomRestaurantFactory $randomRestaurantFactory
     */
    public function __construct(RandomRestaurantFactory $randomRestaurantFactory)
    {
        parent::__construct();
        $this->randomRestaurantFactory = $randomRestaurantFactory;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:generate:restaurants')
            ->setDescription('Generate random restaurants data')
            ->addOption('number', null, InputOption::VALUE_OPTIONAL, 'Number of restaurants', 100);
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
        $question = new ConfirmationQuestion('Do you really want generate random restaurants (y/F)? ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $number = $input->getOption('number');
        $this->randomRestaurantFactory->createAll($number);
        $output->writeln('<success>Restaurants Created</success>');
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