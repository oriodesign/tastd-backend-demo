<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Doctrine\Common\Util\Debug;
use FOS\RestBundle\View\View;
use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class LeaderController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.leader_controller")
 */
class LeaderController extends BaseServiceController
{
    /** @var UserRepository */
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @ApiDoc(
     *  description="Leader array of id",
     *  statusCodes={200="Leaders"},
     *  section="Leader",
     *  filters={
     *      {"name"="leadersOf", "dataType"="integer", "required"=true}
     *  })
     * @Route("/api/leaders/ids")
     * @Cache(maxage="+1 week", public=true)
     * @Method({"GET"})
     * @throws BadRequestException
     * @return View
     */
    public function getLeadersIdsAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $userId = $request->query->get('leadersOf');

        // Handle deprecated parameter: client < 1.7.2
        if (!$userId) {
            $userId = $request->query->get('user');
            $request->query->set('leadersOf', $userId);
        }

        if (!$userId) {
            throw new BadRequestException();
        }

        $leaders = $this->userRepository->getLeadersOf($userId);
        $this->cacheManager->tagController($request);

        return $this->view(array('leaders' => $leaders));
    }
}
