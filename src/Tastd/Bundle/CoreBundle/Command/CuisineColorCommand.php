<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;

/**
 * Class CuisineColorCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class CuisineColorCommand extends Command
{

    protected $cuisineRepository;
    protected $entityManager;

    /**
     * @param CuisineRepository $cuisineRepository
     * @param EntityManager $entityManager
     */
    public function __construct(CuisineRepository $cuisineRepository, EntityManager $entityManager)
    {
        parent::__construct();
        $this->cuisineRepository = $cuisineRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:cuisine:color')
            ->setDescription('Generate cuisine colors');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $colors = array();
        $cuisines = $this->cuisineRepository->getAllAlphabetical(new Request());
        $output->writeln('There are ' . count($cuisines) . ' cuisines');

        $steps = array('c44b7f', '9a3a3d', 'd35823', '309960','6a69ad');
        $coloursPerStep = ceil(count($cuisines) / (count($steps)-1) );

        for ($i=0; $i < count($steps) - 1; $i++) {
            $results = $this->getColors($steps[$i], $steps[$i+1], $coloursPerStep);
            array_pop($results);
            $colors = array_merge($colors, $results);
        }

        for ($i=0; $i < count($cuisines); $i++) {
            $output->writeln($cuisines[$i]->getName() . ' is ' . $colors[$i]);
            $cuisines[$i]->setColor($colors[$i]);
        }

        $this->entityManager->flush();

    }


    public function getColors($begin, $end, $steps = 10)
    {
        $results = array();
        $theColorBegin = hexdec($begin);
        $theColorEnd = hexdec($end);
        $theNumSteps = $steps;

        $theColorBegin = (($theColorBegin >= 0x000000) && ($theColorBegin <= 0xffffff)) ? $theColorBegin : 0x000000;
        $theColorEnd = (($theColorEnd >= 0x000000) && ($theColorEnd <= 0xffffff)) ? $theColorEnd : 0xffffff;
        $theNumSteps = (($theNumSteps > 0) && ($theNumSteps < 256)) ? $theNumSteps : 16;

        $theR0 = ($theColorBegin & 0xff0000) >> 16;
        $theG0 = ($theColorBegin & 0x00ff00) >> 8;
        $theB0 = ($theColorBegin & 0x0000ff) >> 0;

        $theR1 = ($theColorEnd & 0xff0000) >> 16;
        $theG1 = ($theColorEnd & 0x00ff00) >> 8;
        $theB1 = ($theColorEnd & 0x0000ff) >> 0;


        for ($i = 0; $i <= $theNumSteps; $i++) {
            $theR = $this->interpolate($theR0, $theR1, $i, $theNumSteps);
            $theG = $this->interpolate($theG0, $theG1, $i, $theNumSteps);
            $theB = $this->interpolate($theB0, $theB1, $i, $theNumSteps);

            $theVal = ((($theR << 8) | $theG) << 8) | $theB;
            $results[] = dechex($theVal);
        }

        return $results;
    }

    /**
     * @param $pBegin
     * @param $pEnd
     * @param $pStep
     * @param $pMax
     * @return mixed
     */
    public function interpolate($pBegin, $pEnd, $pStep, $pMax)
    {
        if ($pBegin < $pEnd) {
            return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
        } else {
            return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
        }
    }


}