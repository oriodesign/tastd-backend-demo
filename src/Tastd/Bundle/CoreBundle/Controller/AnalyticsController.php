<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tastd\Bundle\CoreBundle\Repository\ConnectionRepository;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Repository\RestaurantRepository;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class AnalyticsController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.analytics_controller")
 */
class AnalyticsController extends BaseServiceController
{

    protected $restaurantRepository;
    protected $userRepository;
    protected $connectionRepository;
    protected $cuisineRepository;
    protected $reviewRepository;

    /**
     * @param RestaurantRepository $restaurantRepository
     * @param UserRepository $userRepository
     * @param ConnectionRepository $connectionRepository
     * @param CuisineRepository $cuisineRepository
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(
        RestaurantRepository $restaurantRepository,
        UserRepository $userRepository,
        ConnectionRepository $connectionRepository,
        CuisineRepository $cuisineRepository,
        ReviewRepository $reviewRepository
    )
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->userRepository = $userRepository;
        $this->connectionRepository = $connectionRepository;
        $this->cuisineRepository = $cuisineRepository;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @ApiDoc(
     *  description="Counters",
     *  statusCodes={200="Success"},
     *  section="Analytics"
     * )
     * @Route("/analytics/counters")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function countersAction()
    {
        $restaurantCount = $this->restaurantRepository->countAll();
        $userCount = $this->userRepository->countAll();
        $connectionCount = $this->connectionRepository->countAll();
        $cuisineCount = $this->cuisineRepository->countAll();
        $reviewCount = $this->reviewRepository->countAll();

        return $this->view(
            array(
                'connectionCount' => $connectionCount,
                'cuisineCount' => $cuisineCount,
                'restaurantCount' => $restaurantCount,
                'reviewCount' => $reviewCount,
                'userCount' => $userCount
            )
        );
    }

    /**
     * @ApiDoc(
     *  description="Count Daily",
     *  statusCodes={200="Success"},
     *  section="Analytics",
     *  filters={
     *      {"name"="from", "dataType"="date"},
     *      {"name"="to", "dataType"="date"}
     *    }
     * )
     * @Route("/analytics/counters/daily")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function countDailyAction()
    {

        $request = $this->requestStack->getCurrentRequest();
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $from = $from ? new \DateTime($from) : new \DateTime('-1 month');
        $to = $to ? new \DateTime($to) : new \DateTime();
        $restaurantCount = $this->restaurantRepository->count($from, $to);
        $reviewCount = $this->reviewRepository->count($from, $to);
        $userCount = $this->userRepository->count($from, $to);
        $connectionCount = $this->connectionRepository->count($from, $to);

        return $this->view(array(
            'connectionCount' => $connectionCount,
            'restaurantCount' => $restaurantCount,
            'reviewCount' => $reviewCount,
            'userCount' => $userCount
        ));
    }

    /**
     * @ApiDoc(
     *  description="Top Follower and Leaders",
     *  statusCodes={200="Success"},
     *  section="Analytics"
     * )
     * @Route("/analytics/top-followers-and-leaders")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function topFollowersAndLeadersAction()
    {
        $topLeaders = $this->connectionRepository->getTopLeaders();
        $topFollowers = $this->connectionRepository->getTopFollowers();

        return $this->view(array(
            'topLeaders' => $topLeaders,
            'topFollowers' => $topFollowers
        ));
    }

    /**
     * @ApiDoc(
     *  description="Top Cuisines",
     *  statusCodes={200="Success"},
     *  section="Analytics"
     * )
     * @Route("/analytics/top-cuisines")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function topCuisinesAction()
    {
        $topCuisines = $this->cuisineRepository->getTopRankedCuisines();

        return $this->view(array(
            'topRankedCuisines' => $topCuisines
        ));
    }

}