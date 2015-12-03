<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\GoogleMapsProvider;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Google\GeocoderClient;
use Tastd\Bundle\CoreBundle\Key\GeocodePrecision;
use Tastd\Bundle\CoreBundle\Key\SerializationGroup;
use Tastd\Bundle\CoreBundle\Manager\AddressManager;

/**
 * Class GeocodeController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.geocode_controller")
 */
class GeocodeController extends BaseServiceController
{

    protected $geocoder;
    protected $addressManager;

    public function __construct(GeocoderClient $geocoder, AddressManager $addressManager)
    {
        $this->geocoder = $geocoder;
        $this->addressManager = $addressManager;
    }

    /**
     * @ApiDoc(
     *  description="Geocode address",
     *  statusCodes={200="Address Geo coded"},
     *  section="Geocode",
     *  filters={
     *      {"name"="query", "dataType"="string", "required"=true},
     *      {"name"="precision", "dataType"="string", "required"=false, "pattern"="CITY|STREET_NUMBER|COUNTRY"}
     *  })
     * @Route("/api/geocode")
     * @throws BadRequestException
     * @Method({"GET"})
     * @Cache(maxage="+1 week", public=true)
     * @return View
     */
    public function geocodeAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $query = $request->query->get('query');
        $precision = $request->query->get('precision', GeocodePrecision::CITY);
        if (!$query) {
            throw new BadRequestException(array('exception.bad_request.missing_parameters'));
        }
        $address = $this->geocoder->geocode($query, $precision);
        if ($address && $address->getCity()) {
            $this->addressManager->hydrateGeoname($address);
        }

        return $this->view(array('addresses' => array($address)), 200, array(), array(SerializationGroup::GEOCODE));
    }

    /**
     * @ApiDoc(
     *  description="Reverse Geo coded address",
     *  statusCodes={200="Address Geo coded"},
     *  section="Geocode",
     *  filters={
     *      {"name"="lat", "dataType"="string", "required"=true},
     *      {"name"="lng", "dataType"="string", "required"=true}
     *  })
     * @Route("/api/reverse-geocode")
     * @throws BadRequestException
     * @Cache(maxage="+1 week", public=true)
     * @Method({"GET"})
     * @return View
     */
    public function reverseGeocodeAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $lat = $request->query->get('lat');
        $lng = $request->query->get('lng');
        if (!$lat || !$lat) {
            throw new BadRequestException(array('exception.bad_request.missing_parameters'));
        }
        $address = $this->geocoder->reverse($lat, $lng);
        $this->addressManager->hydrateGeoname($address);

        return $this->view(array('addresses' => array($address)), 200, array(), array(SerializationGroup::GEOCODE));
    }
}