<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Manager\TagManager;

/**
 * Class TagCountCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class TagCountCommand extends Command
{
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;
    /** @var TagManager  */
    protected $tagManager;

    /**
     * @param TagManager $tagManager
     */
    public function __construct(TagManager $tagManager)
    {
        parent::__construct();
        $this->tagManager = $tagManager;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:tag:count')
            ->setDescription('Update Count for tags');
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
        $this->tagManager->updateCount();
        $this->output->writeln('Tags count updated.');
    }

}