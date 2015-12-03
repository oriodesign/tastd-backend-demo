<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;
use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * Class ApiController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.api_controller")
 */
class ApiController extends BaseServiceController
{


    /**
     * @ApiDoc(
     *  description="Check Api Status",
     *  statusCodes={200="Success"},
     *  section="Api"
     * )
     * @Route("/api/check")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     */
    public function checkAction()
    {
        return $this->view(array('status'=>'available', 'null'=> null));
    }

}
