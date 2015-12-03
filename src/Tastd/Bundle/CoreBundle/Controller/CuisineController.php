<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Doctrine\Common\Util\Debug;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;


/**
 * Class CuisineController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.cuisine_controller")
 */
class CuisineController extends BaseServiceController
{

    protected $cuisineRepository;

    /**
     * @param CuisineRepository $cuisineRepository
     */
    public function __construct(CuisineRepository $cuisineRepository)
    {
        $this->cuisineRepository = $cuisineRepository;
    }

    /**
     * @ApiDoc(
     *  description="Cuisine list",
     *  statusCodes={200="Cuisine"},
     *  section="Cuisine")
     * @Route("/api/cuisines")
     * @Cache(maxage="+1 day", public=true)
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $cuisines = $this->cuisineRepository->getAllAlphabetical($request);
        $this->cacheManager->tagController($request, CacheTag::CUISINE);

        return $this->view(array('cuisines'=>$cuisines));
    }
}