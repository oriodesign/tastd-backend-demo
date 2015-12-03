<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Google\GooglePlaceClient;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class GooglePlaceController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.google_place_controller")
 */
class GooglePlaceController extends BaseServiceController
{
    /** @var GooglePlaceClient */
    protected $googlePlaceClient;
    /** @var GeonameRepository  */
    protected $geonameRepository;

    /**
     * @param GooglePlaceClient $googlePlaceClient
     * @param GeonameRepository $geonameRepository
     */
    public function __construct(GooglePlaceClient $googlePlaceClient, GeonameRepository $geonameRepository)
    {
        $this->googlePlaceClient = $googlePlaceClient;
        $this->geonameRepository = $geonameRepository;
    }

    /**
     * @ApiDoc(
     *  description="GooglePlace search",
     *  statusCodes={200="Address GooglePlace"},
     *  section="Google Place",
     *  filters={
     *      {"name"="id", "dataType"="string", "required"=true},
     *      {"name"="max-width", "dataType"="string", "required"=false}
     *  })
     * @Route("/api/google-places/image")
     * @throws BadRequestException
     * @Method({"GET"})
     * @Cache(maxage="+1 week", public=true)
     * @return View
     */
    public function getImage()
    {
        $request = $this->requestStack->getCurrentRequest();
        $id = $request->query->get('id');
        $maxWidth = $request->query->get('max-width', 400);
        $apiResponse = $this->googlePlaceClient->image($id, $maxWidth);
        $headers = $apiResponse->getHeaders();

        $response = new Response($apiResponse->getContent(), 200, array(
            'Content-Type' => $apiResponse->getHeader('Content-Type'),
            'Content-Disposition' => 'inline;filename=""',
            'Content-Length' => $apiResponse->getHeader('Content-Length')
        ));

        return $response;
    }

    /**
     * @ApiDoc(
     *  description="GooglePlace search",
     *  statusCodes={200="Address GooglePlace"},
     *  section="Google Place",
     *  filters={
     *      {"name"="name", "dataType"="string", "required"=true},
     *      {"name"="latitude", "dataType"="string", "required"=true},
     *      {"name"="longitude", "dataType"="string", "required"=true},
     *      {"name"="radius", "dataType"="integer", "required"=false}
     *  })
     * @Route("/api/google-places/place-results")
     * @throws BadRequestException
     * @Method({"GET"})
     * @Cache(maxage="+1 week", public=true)
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $name = $request->query->get('name');
        $radius = $request->query->get('radius', 50000);
        if (!$latitude || !$longitude ) {
            throw new BadRequestException(array('exception.bad_request.missing_parameters'));
        }
        $placeResultCollection = $this->googlePlaceClient->search($latitude, $longitude, $name, $radius);

        return $this->view(array('placeResults' => $placeResultCollection->toArray()));
    }

    /**
     * @ApiDoc(
     *  description="GooglePlace autocomplete",
     *  statusCodes={200="Address Autocomplete"},
     *  section="Google Place",
     *  filters={
     *      {"name"="q", "dataType"="string", "required"=true},
     *      {"name"="offset", "dataType"="integer", "required"=true},
     *      {"name"="latitude", "dataType"="float", "required"=false},
     *      {"name"="longitude", "dataType"="float", "required"=false},
     *      {"name"="radius", "dataType"="integer", "required"=false},
     *      {"name"="language", "dataType"="integer", "required"=false},
     *      {"name"="types", "dataType"="integer", "required"=false},
     *  })
     * @Route("/api/google-places/autocomplete-results")
     * @throws BadRequestException
     * @Method({"GET"})
     * @Cache(maxage="+1 week", public=true)
     * @return View
     */
    public function autocomplete()
    {
        $request = $this->requestStack->getCurrentRequest();
        $q = $request->query->get('q');
        if (!$q) {
            throw new BadRequestException(array('exception.bad_request.missing_parameters'));
        }
        $cities = $this->googlePlaceClient->autocomplete($request);

        return array('autocompleteResults' => $cities);
    }

    /**
     * @param int $id
     *
     * @ApiDoc(
     *  description="GooglePlace search",
     *  statusCodes={200="Address GooglePlace"},
     *  section="Google Place")
     * @Route("/api/google-places/place-results/{id}")
     * @throws BadRequestException
     * @Method({"GET"})
     * @Cache(maxage="+1 week", public=true)
     * @return View
     */
    public function getAction($id)
    {
        $place = $this->googlePlaceClient->detail($id);
        $address = new Address();
        $address->setCity($place->getCity());
        $address->setLat($place->getLatitude());
        $address->setLng($place->getLongitude());
        $geoname = $this->geonameRepository->getOneByAddress($address);
        $place->setGeoname($geoname);

        return $this->view(array('placeResult' => $place));
    }

}