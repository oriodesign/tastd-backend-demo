<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Stopwatch\Stopwatch;
use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class Public Api Controller
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.public_api_controller")
 */
class PublicApiController extends BaseServiceController
{
    /** @var array */
    protected $clientConfig;

    protected $cuisineRepository;

    protected $geonameRepository;

    /**
     * @param $clientConfig
     * @param CuisineRepository $cuisineRepository
     * @param GeonameRepository $geonameRepository
     */
    public function __construct($clientConfig,
        CuisineRepository $cuisineRepository,
        GeonameRepository $geonameRepository)
    {
        $this->clientConfig = $clientConfig;
        $this->cuisineRepository = $cuisineRepository;
        $this->geonameRepository = $geonameRepository;
    }

    /**
     * @ApiDoc(
     *  description="Client Configuration",
     *  statusCodes={200="Success"},
     *  section="Public Api"
     * )
     * @Route("/public-api/client-config")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function clientConfigAction()
    {
        return $this->view($this->clientConfig);
    }

    /**
     * @ApiDoc(
     *  description="Check Public Api Status",
     *  statusCodes={200="Success"},
     *  section="Public Api"
     * )
     * @Route("/public-api/check")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function checkAction()
    {
        return $this->view(array('status'=>'available', 'null'=> null));
    }

    /**
     * @ApiDoc(
     *  description="Check Public Api Status",
     *  statusCodes={200="Success"},
     *  section="Public Api"
     * )
     * @Route("/public-api/performance")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function performanceAction()
    {
        $stopwatch = new Stopwatch();

        $stopwatch->start('main');

        $address = new Address();
        $address->setCity('London');
        $address->setCountry('GB');
        $address->setLat('51.5286416');
        $address->setLng('-0.1015987');
        $geoname = $this->geonameRepository->getOneByAddress($address);

        $stopwatch->lap('main');

        $cuisines = $this->cuisineRepository->findAll();

        $event = $stopwatch->stop('main');

        $duration = $event->getDuration();
        $endTime = $event->getEndTime();
        $memory = $event->getMemory();
        $periodsData = array();

        foreach ($event->getPeriods() as $period ) {
            $periodsData[] = array(
                'duration' => $period->getDuration(),
                'memory' => $period->getMemory()
            );
        }

        return $this->view(array(
            'duration' => $duration,
            'endTime' => $endTime,
            'memory' => $memory,
            'periods' => $periodsData,
            'geoname' => $geoname,
            'cuisines' => $cuisines,
            ));
    }


}
