<?php

namespace Tastd\Bundle\CoreBundle\Command\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Tastd\Bundle\CoreBundle\Entity\Formatter\EntityFormatterBag;

class Printer
{
    /** @var OutputInterface */
    protected $output;
    /** @var EntityFormatterBag */
    protected $entityFormatterBag;

    /**
     * @param EntityFormatterBag $entityFormatterBag
     */
    public function __construct(EntityFormatterBag $entityFormatterBag)
    {
        $this->entityFormatterBag = $entityFormatterBag;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param $data
     */
    public function writelnEntity($data)
    {
        $string = $this->entityFormatterBag->getOneLineDescription($data);
        $this->output->writeln($string);
    }

    /**
     * @param $data
     */
    public function writelnEntityShort($data)
    {
        $string = $this->entityFormatterBag->getOneLineShortDescription($data);
        $this->output->writeln($string);
    }

    /**
     * new Line
     */
    public function newLine()
    {
        $this->output->writeln('');
    }

}
