<?php

namespace Tastd\Bundle\CoreBundle\Google;

use Buzz\Browser;
use Buzz\Message\MessageInterface;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Factory\AddressFactory;
use Tastd\Bundle\CoreBundle\Exception\Api\Google\GoogleAccessDeniedException;
use Tastd\Bundle\CoreBundle\Exception\Api\Google\GoogleQuotaExceededException;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Google\Place\AutocompleteResult;
use Tastd\Bundle\CoreBundle\Google\Place\FullPlaceResult;
use Tastd\Bundle\CoreBundle\Google\Place\PlaceResult;
use Tastd\Bundle\CoreBundle\Google\Place\PlaceResultCollection;
use Tastd\Bundle\CoreBundle\Google\Place\PlaceResultFactory;
use Tastd\Bundle\CoreBundle\Http\Url;

/**
 * Class GooglePlaceClient
 *
 * @package Tastd\Bundle\CoreBundle\Google
 */
class GooglePlaceClient
{
    const TYPES = 'restaurant|food|bakery|bar|meal_takeaway';

    const SEARCH_URL       = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?';
    const AUTOCOMPLETE_URL = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?';
    const DETAIL_URL       = 'https://maps.googleapis.com/maps/api/place/details/json?';
    const IMAGE_URL        = 'https://maps.googleapis.com/maps/api/place/photo?';

    protected $apiKey;
    protected $browser;
    protected $placeResultFactory;
    protected $addressFactory;

    /**
     * __construct
     * @param PlaceResultFactory $placeResultFactory
     * @param AddressFactory     $addressFactory
     * @param string             $apiKey
     * @param Browser            $browser
     */
    public function __construct(
        PlaceResultFactory $placeResultFactory,
        AddressFactory $addressFactory,
        $apiKey,
        Browser $browser)
    {
        $this->apiKey = $apiKey;
        $this->placeResultFactory = $placeResultFactory;
        $this->addressFactory = $addressFactory;
        $this->browser = $browser;
    }

    /**
     * @param int $id
     * @param int $maxWidth
     *
     * @return MessageInterface
     */
    public function image($id, $maxWidth = 400)
    {
        $params = array(
            'key' => $this->apiKey,
            'photoreference' => $id,
            'maxwidth' => $maxWidth
        );

        $response = $this->browser->get(new Url(self::IMAGE_URL, $params));

        return $response;
    }

    /**
     * @param string $latitude
     * @param string $longitude
     * @param string $name
     * @param int    $radius
     * @param string $nextPage
     *
     * @throws BadRequestException
     *
     * @return PlaceResultCollection
     */
    public function search($latitude, $longitude, $name = null, $radius = 1000, $nextPage = null)
    {
        $params = array(
            'types'=> self::TYPES,
            'location' => $latitude.','.$longitude,
            'radius' => $radius,
            'language'=> 'en',
            'key' => $this->apiKey);

        if ($name) {
            $params['name'] = $name;
        }
        if ($nextPage) {
            $params['pagetoken'] = $nextPage;
        }

        try {
            $response= $this->browser->get(new Url(self::SEARCH_URL, $params));
        } catch (\Exception $e) {
            $exception = new BadRequestException();
            $exception->setInfo($e->getMessage());
            throw $exception;
        }

        $data = $this->parseResponse($response);

        return $data;
    }

    /**
     * @param Request $request
     *
     * @throws BadRequestException
     * @return array
     */
    public function autocomplete($request)
    {
        $query = $request->query->get('q');
        $lat = $request->query->get('lat');
        $lng = $request->query->get('lng');
        $offset = $request->query->get('offset');
        $radius = $request->query->get('radius');
        $types = $request->query->get('types', 'establishment');
        $language = $request->query->get('language');

        $params = array(
            'input'=> $query,
            'key' => $this->apiKey,
            'types' => $types
        );

        if ($language) {
            $params['language'] = $language;
        }

        if ($offset) {
            $params['offset'] = $offset;
        }

        if ($radius) {
            $params['radius'] = $radius;
        }

        if ($lat && $lng) {
            $params['location'] = $lat . ',' . $lng;
        }

        try {
            $response = $this->browser->get(new Url(self::AUTOCOMPLETE_URL, $params));
        } catch (\Exception $e) {
            $exception = new BadRequestException();
            $exception->setInfo($e->getMessage());
            throw $exception;
        }

        return $this->parseAutocompleteResponse($response);
    }

    /**
     * @param integer $id
     *
     * @return FullPlaceResult
     * @throws \Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException
     */
    public function detail($id)
    {
        $params = array(
            'placeid'=> $id,
            'key' => $this->apiKey);

        try {
            $response = $this->browser->get(new Url(self::DETAIL_URL, $params));
        } catch (\Exception $e) {
            $exception = new BadRequestException();
            $exception->setInfo($e->getMessage());
            throw $exception;
        }

        return $this->parseDetailResponse($response);
    }

    /**
     * @param MessageInterface $response
     *
     * @return FullPlaceResult
     * @throws BadRequestException
     */
    public function parseDetailResponse(MessageInterface $response)
    {
        $data = json_decode($response->getContent(), true);
        if ($this->isOkGooglePlaceData($data)) {
            return $this->placeResultFactory->createDetail($data['result']);
        }

        return $this->handleGooglePlaceApiErrors($data);
    }

    /**
     * @param MessageInterface $response
     *
     * @return array
     */
    public function parseAutocompleteResponse(MessageInterface $response)
    {
        $data = json_decode($response->getContent(), true);
        if ($this->isOkGooglePlaceData($data)) {
            $cities = array();
            foreach ($data['predictions'] as $prediction) {
                $autocompleteResult = new AutocompleteResult();
                $autocompleteResult->setId($prediction['place_id']);
                $autocompleteResult->setName($prediction['description']);
                $cities[] = $autocompleteResult;
            }

            return $cities;
        }

        return $this->handleGooglePlaceApiErrors($data);
    }

    /**
     * @param MessageInterface $response
     *
     * @return mixed
     * @throws BadRequestException
     */
    public function parseResponse($response)
    {
        $data = json_decode($response->getContent(), true);
        if ($this->isOkGooglePlaceData($data)) {
            return $this->placeResultFactory->createCollection($data);
        }

        return $this->handleGooglePlaceApiErrors($data, new PlaceResultCollection());
    }


    /**
     * @param mixed $response
     * @param mixed $zeroResults
     *
     * @return mixed
     *
     * @throws \Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException
     * @throws \Tastd\Bundle\CoreBundle\Exception\Api\Google\GoogleQuotaExceededException
     * @throws \Tastd\Bundle\CoreBundle\Exception\Api\Google\GoogleAccessDeniedException
     */
    public function handleGooglePlaceApiErrors($response, $zeroResults = array())
    {
        if (!is_array($response)) {
            throw new BadRequestException(array(), 'exception.http.bad_request', 'Empty Response');
        } else if (!array_key_exists('status', $response)) {
            throw new BadRequestException();
        } else if ($response['status'] === 'OVER_QUERY_LIMIT') {
            throw new GoogleQuotaExceededException();
        } else if ($response['status'] === 'REQUEST_DENIED') {
            throw new GoogleAccessDeniedException();
        } else if ($response['status'] === 'INVALID_REQUEST') {
            throw new BadRequestException(array(), 'exception.http.bad_request', 'Invalid request to google');
        } else if ($response['status'] === 'UNKNOWN_ERROR') {
            throw new BadRequestException(array(), 'exception.http.bad_request', 'Unknown error from google');
        } else if ($response['status'] === 'ZERO_RESULTS') {
            return $zeroResults;
        } else if ($response['status'] === 'NOT_FOUND') {
            return $zeroResults;
        }

        throw new BadRequestException(array('google' => 'unexpected status for google response'));
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function isOkGooglePlaceData($data)
    {
        if (is_array($data) && array_key_exists('status', $data)) {
            if ($data['status']=== 'OK') {
                return true;
            }

            return false;
        }
    }


}