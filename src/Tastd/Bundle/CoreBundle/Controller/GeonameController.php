<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use ArrayIterator;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Tastd\Bundle\CoreBundle\Entity\Geoname;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Google\ReverseGeonamer;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class GeocodeController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.geoname_controller")
 */
class GeonameController extends BaseServiceController
{
    /** @var GeonameRepository */
    protected $geonameRepository;

    /** @var ReverseGeonamer  */
    protected $reverseGeonamer;

    /** @var UserRepository */
    protected $userRepository;

    /**
     * @param GeonameRepository $geonameRepository
     * @param ReverseGeonamer   $reverseGeonamer
     * @param UserRepository    $userRepository
     */
    public function __construct(
        GeonameRepository $geonameRepository,
        ReverseGeonamer $reverseGeonamer,
        UserRepository $userRepository)
    {
        $this->geonameRepository = $geonameRepository;
        $this->reverseGeonamer = $reverseGeonamer;
        $this->userRepository = $userRepository;
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Geoname detail",
     *  statusCodes={200="Geoname"},
     *  section="Geoname")
     * @Route("/api/geonames/{id}")
     * @Route("/public-api/geonames/{id}")
     * @Method({"GET"})
     * @return View
     * @Cache(maxage="+1 week", public=true)
     */
    public function getAction($id)
    {
        $geoname = $this->geonameRepository->get($id);

        return $this->view(array('geoname' => $geoname));
    }

    /**
     * @ApiDoc(
     *  description="Geonames",
     *  statusCodes={200="Geonames"},
     *  section="Geoname",
     *  filters={
     *      {"name"="asciiName", "dataType"="string", "required"="false"},
     *      {"name"="country", "dataType"="string", "required"="false"},
     *      {"name"="user", "dataType"="integer", "required"="false"},
     *      {"name"="orderBy", "dataType"="string", "required"="false", "pattern"="distance"},
     *      {"name"="lat", "dataType"="string", "required"="false"},
     *      {"name"="lng", "dataType"="string", "required"="false"},
     *      {"name"="featured", "dataType"="boolean", "required"="false"}
     *  })
     * @Route("/api/geonames")
     * @Route("/public-api/geonames")
     * @Cache(maxage="+1 week", public=true)
     * @throws BadRequestException
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $orderBy = $request->query->get('orderBy');
        $featured = $request->query->get('featured');
        $userId = $request->query->get('user');
        $user = $userId ? $this->userRepository->get($userId) : null;

        if ($featured === 'true') {
            return $this->getFeaturedGeonames();
        }

        // @TODO if order by distance use the real name with reverse geocode to find the right city
        if ($orderBy === 'distance') {
            $pager = $this->reverseGeonamer->reverseWithFallback($request);
        } else {
            $pager = $this->geonameRepository->getAll($request, $user);
        }
        $this->cacheManager->tagController($request, CacheTag::GEONAME);

        return $this->view($this->getPagedViewData($pager, 'geonames'));
    }

    /**
     * getFeaturedGeonames
     */
    protected function getFeaturedGeonames()
    {
        $geonames = $this->geonameRepository->getFeatured();

        return $this->view(array('geonames' => $geonames));
    }

}