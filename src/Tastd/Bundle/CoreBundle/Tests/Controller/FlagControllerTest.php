<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;

/**
 * Class FlagControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class FlagControllerTest extends RestControllerTestCase
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
        $p1 = array(
            'geoname' => 6,
            'users' => '1,2,3,4',
            'cuisines' => '1,2,3,4',
            'minCost' => 10,
            'maxCost' => 100,
            'tags' => 'crazyparty,chic,expensive'
        );

        $p2 = array(
            'geoname' => 6,
            'users' => '1,2',
            'cuisines' => '1,2,3,4,5,6,7,8,9,10,11',
            'minCost' => 10,
            'maxCost' => 100,
        );

        $p3 = array(
            'geoname' => 6,
            'users' => '1,2,3,4'
        );

        return array(
            array($p1),
            array($p2),
            array($p3)
        );
    }

    /**
     * @param $parameters
     *
     * @dataProvider parameterProvider
     */
    public function testGetAll($parameters)
    {
        $client = static::createClient();

        $this->getRequest($client, 'api/flags', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}