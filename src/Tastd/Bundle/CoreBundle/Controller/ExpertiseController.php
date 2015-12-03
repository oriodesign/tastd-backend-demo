<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\AccessDeniedException;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;
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
use Tastd\Bundle\CoreBundle\Repository\WishRepository;

/**
 * Class ExpertiseController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.expertise_controller")
 */
class ExpertiseController extends BaseServiceController
{
    /**
     * @var ReviewRepository
     */
    protected $reviewRepository;

    /**
     * @var WishRepository
     */
    protected $wishRepository;

    /**
     * @param ReviewRepository $reviewRepository
     * @param WishRepository   $wishRepository
     */
    public function __construct(ReviewRepository $reviewRepository, WishRepository $wishRepository)
    {
        $this->reviewRepository = $reviewRepository;
        $this->wishRepository = $wishRepository;
    }

    /**
     * @ApiDoc(
     *  description="Expertise list",
     *  statusCodes={200="Expertise"},
     *  section="Expertise",
     *  filters={
     *      {"name"="user", "dataType"="integer", "required"=true},
     *      {"name"="wish", "dataType"="boolean", "required"=false},
     *      {"name"="groupBy", "dataType"="string", "required"=false, "pattern"="cuisine|geoname"}
     *  })
     * @Route("/api/expertise")
     * @Route("/public-api/expertise")
     * @Method({"GET"})
     * @throws BadRequestException
     * @Cache(maxage="+1 day", public=true)
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = (int) $this->requestStack->getCurrentRequest()->query->get('user');
        $groupBy = $this->requestStack->getCurrentRequest()->query->get('groupBy');
        $wish = (bool) $this->requestStack->getCurrentRequest()->query->get('wish');
        if (!isset($user)) {
            throw new BadRequestException(array('exception.bad_request.missing_parameters'));
        }
        if ($wish) {
            return $this->getWishList();
        }

        $expertiseList = $this->reviewRepository->getExpertiseByUser($user, $groupBy);

        $this->cacheManager->tagController($request, CacheTag::REVIEW);

        return $this->view(array('expertise' => $expertiseList));
    }

    /**
     * @return View
     * @throws AccessDeniedException
     */
    private function getWishList()
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = (int) $this->requestStack->getCurrentRequest()->query->get('user');
        $groupBy = $this->requestStack->getCurrentRequest()->query->get('groupBy');
        if ($user !== $this->getUser()->getId()) {
            throw new AccessDeniedException();
        }
        $expertiseList = $this->wishRepository->getExpertiseByUser($user, $groupBy);

        $this->cacheManager->tagController($request, CacheTag::WISH);

        return $this->view(array('expertise' => $expertiseList));
    }
}