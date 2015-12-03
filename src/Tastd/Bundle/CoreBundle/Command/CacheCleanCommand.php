<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Cache\CacheManager;

/**
 * Class CacheCleanCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class CacheCleanCommand extends Command
{

    protected $cacheManager;

    /**
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        parent::__construct();
        $this->cacheManager = $cacheManager;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:cache:clean')
            ->setDescription('Clean varnish cache')
            ->addArgument('tag', InputArgument::REQUIRED, 'Which tag clean');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $tag = $input->getArgument('tag');
        $this->cacheManager->invalidateTags(array($tag));
    }

}