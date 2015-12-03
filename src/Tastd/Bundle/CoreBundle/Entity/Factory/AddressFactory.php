<?php

namespace Tastd\Bundle\CoreBundle\Entity\Factory;

use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Exception\Api\Geo\UnresolvedGeonameException;
use Tastd\Bundle\CoreBundle\Google\Place\FullPlaceResult;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class AddressFactory
 *
 * @package Tastd\Bundle\CoreBundle\Entity\Factory
 */
class AddressFactory
{
    /** @var GeonameRepository */
    protected $geonameRepository;

    /**
     * @param GeonameRepository $geonameRepository
     */
    public function __construct(GeonameRepository $geonameRepository)
    {
        $this->geonameRepository = $geonameRepository;
    }

    /**
     * @param FullPlaceResult $place
     * @return Address
     *
     * @throws UnresolvedGeonameException
     */
    public function createFromFullPlaceResult(FullPlaceResult $place)
    {
        $address = new Address();
        $address->setCity($place->getCity());
        $address->setLat($place->getLatitude());
        $address->setLng($place->getLongitude());
        $address->setCountry($place->getCountry());
        $address->setCountryCode($place->getCountryCode());
        $address->setFormattedAddress($place->getFormattedAddress());
        $address->setRegion($place->getRegion());
        $address->setRegionCode($place->getRegionCode());
        $address->setCounty($place->getCounty());
        $address->setCountyCode($place->getCountyCode());
        $address->setCity($place->getCity());
        $address->setPostalCode($place->getPostalCode());
        $address->setStreetName($place->getStreetName());
        $address->setStreetNumber($place->getStreetNumber());
        $geoname = $this->geonameRepository->getOneByAddress($address);
        $address->setGeoname($geoname);

        return $address;
    }

}