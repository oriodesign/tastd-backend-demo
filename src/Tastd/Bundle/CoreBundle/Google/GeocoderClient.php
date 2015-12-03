<?php

namespace Tastd\Bundle\CoreBundle\Google;

use Bazinga\Bundle\GeocoderBundle\Geocoder\LoggableGeocoder;
use Geocoder\Exception\InvalidCredentialsException;
use Geocoder\Exception\NoResultException;
use Geocoder\Exception\QuotaExceededException;
use Geocoder\Result\ResultInterface;
use Tastd\Bundle\CoreBundle\Entity\Address;
use Tastd\Bundle\CoreBundle\Exception\Api\Google\GoogleQuotaExceededException;
use Tastd\Bundle\CoreBundle\Exception\Api\Google\InvalidGoogleCredentialException;
use Tastd\Bundle\CoreBundle\Key\GeocodePrecision;
use Tastd\Bundle\CoreBundle\Manager\AddressManager;

/**
 * Class GeocoderClient
 *
 * @package Tastd\Bundle\CoreBundle\Google
 */
class GeocoderClient
{
    protected $geocoder;
    protected $addressManager;

    /**
     * @param LoggableGeocoder $geocoder
     * @param AddressManager   $addressManager
     */
    public function __construct(LoggableGeocoder $geocoder, AddressManager $addressManager)
    {
        $this->geocoder = $geocoder;
        $this->addressManager = $addressManager;
    }

    /**
     * @param string $query
     * @param string $precision
     *
     * @return null|Address
     * @throws InvalidGoogleCredentialException
     * @throws GoogleQuotaExceededException
     */
    public function geocode($query, $precision = null)
    {
        $address = null;
        try {
            $result = $this->geocoder->geocode($query);
            $address = $this->geocoderResultToAddress($result);
            if ($precision && !$this->isAccurate($address, $precision)) {
                return null;
            }
        } catch (NoResultException $e) {
            return null;
        } catch (InvalidCredentialsException $e) {
            throw new InvalidGoogleCredentialException();
        } catch (QuotaExceededException $e) {
            throw new GoogleQuotaExceededException();
        } catch (\Exception $e) {
            return null;
        }

        return $address;
    }

    /**
     * @param string $lat
     * @param string $lng
     *
     * @return null
     * @throws \Tastd\Bundle\CoreBundle\Exception\Api\Google\InvalidGoogleCredentialException
     * @throws \Tastd\Bundle\CoreBundle\Exception\Api\Google\GoogleQuotaExceededException
     */
    public function reverse($lat, $lng)
    {
        try {
            $result = $this->geocoder->reverse($lat, $lng);
            $address = $this->geocoderResultToAddress($result);
        } catch (NoResultException $e) {
            return null;
        } catch (InvalidCredentialsException $e) {
            throw new InvalidGoogleCredentialException();
        } catch (QuotaExceededException $e) {
            throw new GoogleQuotaExceededException();
        } catch (\Exception $e) {
            return null;
        }

        return $address;
    }

    /**
     * @param $lat
     * @param $lng
     * @return array|null
     * @throws GoogleQuotaExceededException
     * @throws InvalidGoogleCredentialException
     */
    public function plainReverse($lat, $lng)
    {
        try {
            $result = $this->geocoder->reverse($lat, $lng);
        } catch (NoResultException $e) {
            return null;
        } catch (InvalidCredentialsException $e) {
            throw new InvalidGoogleCredentialException();
        } catch (QuotaExceededException $e) {
            throw new GoogleQuotaExceededException();
        } catch (\Exception $e) {
            return null;
        }

        return $result;
    }

    /**
     * @param ResultInterface $result
     *
     * @return Address
     */
    public function geocoderResultToAddress(ResultInterface $result)
    {
        $address = new Address();
        $address->setCity($result->getCity());
        $address->setCountry($result->getCountry());
        $address->setCountryCode($result->getCountryCode());
        $address->setRegion($result->getRegion());
        $address->setRegionCode($result->getRegionCode());
        $address->setCounty($result->getCounty());
        $address->setCountyCode($result->getCountyCode());
        $address->setLat($result->getLatitude());
        $address->setLng($result->getLongitude());
        $address->setPostalCode($result->getZipcode());
        $address->setStreetName($result->getStreetName());
        $address->setStreetNumber($result->getStreetNumber());

        $this->addressManager->updateFormattedAddress($address);

        return $address;
    }

    /**
     * @param Address $address
     * @param string  $precision
     *
     * @return bool
     */
    public function isAccurate(Address $address, $precision)
    {
        if ($precision === GeocodePrecision::COUNTRY && is_null($address->getCountry())) {
            return false;
        } else if ($precision === GeocodePrecision::CITY && is_null($address->getCity())) {
            return false;
        } else if ($precision === GeocodePrecision::STREET_NAME && is_null($address->getStreetName())) {
            return false;
        } else if ($precision === GeocodePrecision::STREET_NUMBER && is_null($address->getStreetNumber())) {
            return false;
        }

        return true;
    }

}