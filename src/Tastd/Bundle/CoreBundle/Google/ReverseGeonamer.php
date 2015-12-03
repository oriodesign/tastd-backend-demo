<?php

namespace Tastd\Bundle\CoreBundle\Google;
use Geocoder\Provider\GoogleMapsProvider;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class ReverseGeonamer
 *
 * @package Tastd\Bundle\CoreBundle\Google
 */
class ReverseGeonamer
{

    protected $geonameRepository;
    protected $googleMapProvider;

    /**
     * @param GeonameRepository  $geonameRepository
     * @param GoogleMapsProvider $googleMapProvider
     */
    public function __construct(GeonameRepository $geonameRepository, GoogleMapsProvider $googleMapProvider)
    {
        $this->geonameRepository = $geonameRepository;
        $this->googleMapProvider = $googleMapProvider;
    }


    /**
     * @param Request $request
     *
     * @return array|Pagerfanta
     */
    public function reverseWithFallback(Request $request)
    {
        try {
            $pager = $this->reverse(
                $request->query->get('lat'),
                $request->query->get('lng'),
                $request->query->get('page', 1)
                );
        } catch (\Exception $e) {
            $pager = $this->geonameRepository->getAll($request);
        }

        return $pager;
    }

    /**
     * @param string $lat
     * @param string $lng
     * @param int    $page
     *
     * @return array
     */
    private function reverse($lat, $lng, $page)
    {
        $results = $this->googleMapProvider->getReversedData(array($lat, $lng));
        $results = $this->fillCity($results);

        return $this->geonameRepository->getByAsciiNameCountryLatLng(
            $results[0]['city'],
            $results[0]['countryCode'],
            $results[0]['latitude'],
            $results[0]['longitude'],
            $page);
    }

    /**
     * Google maps can have null city for the first result
     * @param array $results
     *
     * @return array
     */
    private function fillCity($results)
    {
        for ($i = 0; $i < (count($results)-1); $i++) {
            if (null === $results[$i]['city']) {
                $results[$i]['city'] = $results[$i+1]['city'];
            } else {
                return $results;
            }
        }

        return $results;
    }


}