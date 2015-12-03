<?php

namespace Tastd\Bundle\CoreBundle\Google\Place;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PlaceResultFactory
 *
 * @package Tastd\Bundle\CoreBundle\Google\Place
 */
class PlaceResultFactory
{
    /**
     * @param RouterInterface $router
     */

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $data
     *
     * @return FullPlaceResult
     */
    public function createDetail($data)
    {
        $place = new FullPlaceResult();
        $place->setId($data['place_id']);
        $place->setName($data['name']);
        $place->setIcon($data['icon']);
        $place->setLatitude($data['geometry']['location']['lat']);
        $place->setLongitude($data['geometry']['location']['lng']);
        if (isset($data['website'])) {
            $place->setWebsite($data['website']);
        }
        if (isset($data['international_phone_number'])) {
            $place->setTelephone($data['international_phone_number']);
        }
        $this->setAddress($place, $data['address_components']);
        if (isset($data['photos'])) {
            $this->setPhotos($place, $data['photos']);
            $this->setPicture($place, $data['photos']);
        }


        return $place;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function createCollection($data)
    {
        $placeResults = new PlaceResultCollection();
        foreach ($data['results'] as $result) {
            $placeResult = $this->createPlaceResult($result);
            $placeResults->add($placeResult);
        }

        if (isset($data['next_page_token'])) {
            $placeResults->setNextPage($data['next_page_token']);
        }

        return $placeResults;
    }

    /**
     * @param array $data
     *
     * @return PlaceResult
     */
    public function createPlaceResult($data)
    {
        $place = new PlaceResult();
        $place->setId($data['place_id']);
        $place->setName($data['name']);
        $place->setIcon($data['icon']);
        if (isset($data['photos'])) {
            $this->setPicture($place, $data['photos']);
        }

        return $place;
    }

    /**
     * @param FullPlaceResult $place
     * @param array           $photosData
     */
    protected function setPhotos(FullPlaceResult $place, $photosData)
    {
        $photos = array();
        $photosReferences = array();
        foreach ($photosData as $data) {
            $url = $this->router->generate('tastd_core_googleplace_getimage', array('id' => $data['photo_reference']), true);
            $photos[] = str_replace('http', 'https', $url);
            $photosReferences[] = $data['photo_reference'];
        }
        $place->setPhotoReferences($photosReferences);
        $place->setPhotos($photos);
    }

    /**
     * @param PlaceResult $place
     * @param array       $photosData
     */
    protected function setPicture(PlaceResult $place, $photosData)
    {
        if (count($photosData) > 0 && array_key_exists('photo_reference', $photosData[0])) {
            $url = $this->router->generate('tastd_core_googleplace_getimage', array('id' => $photosData[0]['photo_reference']), true);
            $imageUrl = str_replace('http', 'https', $url);
            $place->setPicture($imageUrl);
        }
    }

    /**
     * @param FullPlaceResult $place
     * @param array           $addressesData
     */
    protected function setAddress(FullPlaceResult $place, $addressesData)
    {
        $streetName = '';
        $streetNumber = null;
        $premiseComponents = array();

        foreach ($addressesData as $data) {
            if (in_array('street_number', $data['types'])) {
                $place->setStreetNumber($data['long_name']);
                $streetNumber = $data['long_name'];
            } elseif (in_array('premise', $data['types'])) {
                $premiseComponents[] = $data['long_name'];
            } elseif (in_array('subpremise', $data['types'])) {
                $premiseComponents[] = $data['long_name'];
            } elseif (in_array('route', $data['types'])) {
                $place->setStreetName($data['long_name']);
                $streetName = $data['long_name'];
            } elseif (in_array('locality', $data['types'])) {
                $place->setCity($data['long_name']);
            } elseif (in_array('country', $data['types'])) {
                $place->setCountry($data['long_name']);
                $place->setCountryCode($data['short_name']);
            } elseif (in_array('postal_code', $data['types'])) {
                $place->setPostalCode($data['long_name']);
            } elseif (in_array('administrative_area_level_1', $data['types'])) {
                $place->setRegion($data['long_name']);
                $place->setRegionCode($data['short_name']);
            } elseif (in_array('administrative_area_level_2', $data['types'])) {
                $place->setCounty($data['long_name']);
                $place->setCountyCode($data['short_name']);
            }
        }

        if ($streetNumber !== null && count($premiseComponents) > 0) {
            $formattedAddress = sprintf('%s - %s, %s', implode(' ', $premiseComponents), $streetNumber, $streetName);
        } else if (count($premiseComponents) > 0) {
            $formattedAddress = sprintf('%s, %s', implode(' ', $premiseComponents), $streetName);
        } else if ($streetNumber !== null) {
            $formattedAddress = $streetNumber . ', ' . $streetName;
        } else {
            $formattedAddress = $streetName;
        }

        $place->setFormattedAddress($formattedAddress);
    }
}