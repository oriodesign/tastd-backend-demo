<?php

namespace Tastd\Bundle\CoreBundle\Manager;
use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Repository\GeonameRepository;

/**
 * Class AddressManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class AddressManager
{
    protected $geonameRepository;

    /**
     * @param GeonameRepository $geonameRepository
     */
    public function __construct(GeonameRepository $geonameRepository)
    {
        $this->geonameRepository = $geonameRepository;
    }

    /**
     * @param Address $address
     *
     * @return Address
     */
    public function updateFormattedAddress(Address $address)
    {
        $formattedAddress = $this->generateFormattedAddress($address);
        $address->setFormattedAddress($formattedAddress);

        return $address;
    }

    /**
     * @param Address $address
     */
    public function hydrateGeoname(Address $address)
    {
        $geoname = $this->geonameRepository->getOneByAddress($address);
        $address->setGeoname($geoname);
    }

    /**
     * @param Address $address
     *
     * @return string
     * @throws \Exception
     */
    public function generateFormattedAddress(Address $address)
    {
        $formattedAddress = '';
        $streetName = $address->getStreetName();
        $streetNumber = $address->getStreetNumber();

        if ($streetName && $streetNumber) {
            $formattedAddress = sprintf('%s %s', $streetNumber, $streetName);
        } else if ($streetName) {
            $formattedAddress = $streetName;
        }

        return $formattedAddress;
    }

}