<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;

/**
 * Class AddressController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.address_controller")
 */

class AddressController extends BaseServiceController
{

    /**
     * @ApiDoc(
     *  description="Get addresses with several strategies",
     *  statusCodes={200="Success"},
     *  section="Address",
     *  filters={
     *      {"name"="query", "dataType"="string"},
     *      {"name"="lat", "dataType"="string"},
     *      {"name"="lng", "dataType"="string"},
     *      {"name"="precision", "dataType"="string", "pattern"="CITY|STREET_NUMBER|COUNTRY"}
     *  }
     * )
     * @Route("/api/addresses")
     * @Template{}
     * @Method({"GET"})
     * @return mixed
     * @throws BadRequestException
     * @Cache(smaxage="+1 year")
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $query = $request->query->get('query');
        $precision = $request->query->get('precision');
        $lat = $request->query->get('lat');
        $lng = $request->query->get('lng');

        if ($query) {
            return $this->forward('tastd.geocode_controller:geocodeAction', array(), array(
                'query'=>$query,
                'precision' => $precision)
            );
        }

        if ($lat && $lng) {
            return $this->forward('tastd.geocode_controller:reverseGeocodeAction', array(), array(
                'lat' => $lat,
                'lng' => $lng
            ));
        }

        throw new BadRequestException(array('exception.bad_request.missing_parameters'));
    }

}
