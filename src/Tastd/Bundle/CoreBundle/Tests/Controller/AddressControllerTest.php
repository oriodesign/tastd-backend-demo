<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;

/**
 * Class AddressControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class AddressControllerTest extends RestControllerTestCase
{
    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass()
    {
        self::resetDb();
    }

    /**
     * latLngProvider
     */
    public function latLngProvider()
    {
        $ny = array(
            'lat' => '40.662035',
            'lng' => '-73.886765'
        );
        $mi = array(
            'lat' => '45.5117436',
            'lng' => '9.2391834'
        );
        $lon = array(
            'lat' => '51.495592',
            'lng' => '-0.129191'
        );

        return array(
            array($ny),
            array($mi),
            array($lon)
        );
    }

    /**
     * @dataProvider latLngProvider
     */
    public function testGetByLatLng($parameters)
    {
        $client = static::createClient();

        $this->getRequest($client, 'api/addresses', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * addressProvider
     */
    public function addressProvider()
    {
        return array(
            array('684 Hegeman Ave, New York'),
            array('via de Marchi Gherini 10 Milano'),
            array('Milan via de marchi Gherini 10'),
            array('Irving St 18 London, England')
        );
    }

    /**
     * @dataProvider addressProvider
     */
    public function testGetByQueryAction($address)
    {
        $client = static::createClient();
        $parameters = array(
            'query' => $address,
            'precision' => 'STREET_NUMBER'
        );
        $this->getRequest($client, 'api/addresses', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }


}