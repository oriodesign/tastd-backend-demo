<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;

/**
 * Class ExpertiseControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class ExpertiseControllerTest extends RestControllerTestCase
{
    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass()
    {
        self::resetDb();
    }

    /**
     * @return array
     */
    public function parameterProvider()
    {
        $byGeoname = array(
            'user' => 1,
            'groupBy' => 'geoname'
        );

        $byCuisine = array(
            'user' => 1,
            'groupBy' => 'cuisine'
        );

        $forWishes = array(
            'user' => 1,
            'groupBy' => 'cuisine',
            'wish' => true
        );

        $default = array(
            'user' => 1
        );

        return array(
            array($forWishes),
            array($byGeoname),
            array($byCuisine),
            array($default)
        );
    }

    /**
     * @dataProvider parameterProvider
     */
    public function testGetAction($parameters)
    {
        $client = static::createClient();
        $this->getRequest($client, 'api/expertise', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}