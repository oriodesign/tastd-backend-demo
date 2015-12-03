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
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Repository\FlagRepository;

/**
 * Class FlagController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.flag_controller")
 */
class FlagController extends BaseServiceController
{
    /** @var FlagRepository */
    protected $flagRepository;

    /**
     * @param FlagRepository $flagRepository
     */
    public function __construct(FlagRepository $flagRepository)
    {
        $this->flagRepository = $flagRepository;
    }

    /**
     * @ApiDoc(
     *  description="Flags list",
     *  statusCodes={200="Flags"},
     *  section="Flag",
     *  filters={
     *      {"name"="geoname", "dataType"="integer", "required"=true},
     *      {"name"="wishedBy", "dataType"="integer", "required"=false},
     *      {"name"="reviewedBy", "dataType"="integer", "required"=false},
     *      {"name"="users", "dataType"="string", "required"=false},
     *      {"name"="leadersOf", "dataType"="integer", "required"=false},
     *      {"name"="cuisines", "dataType"="string", "required"=false},
     *      {"name"="minCost", "dataType"="integer", "required"=false},
     *      {"name"="maxCost", "dataType"="integer", "required"=false},
     *      {"name"="tags", "dataType"="string", "required"=false},
     *      {"name"="page", "dataType"="integer", "required"=false},
     *      {"name"="wish", "dataType"="boolean", "required"=false},
     *      {"name"="orderBy", "dataType"="string", "required"=false},
     *      {"name"="withWish", "dataType"="boolean", "required"=false}
     *  })
     * @Route("/api/flags")
     * @Method({"GET"})
     * @throws BadRequestException
     * @Cache(maxage="+1 day", public=true)
     * @return View
     */
    public function getAll()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request->query->get('geoname')) {
            throw new BadRequestException(array('exception.bad_request.missing_parameters'));
        }
        $pager = $this->flagRepository->getFlagsPager($request);
        $cacheTags = $this->cacheManager->getCacheTagsForMixedReviewsAndWishes($request);
        $this->cacheManager->tagController($request, $cacheTags);

        return $this->view($this->getPagedViewData($pager, 'flags'));
    }
}