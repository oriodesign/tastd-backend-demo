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
class GeonameControllerTest extends RestControllerTestCase
{

    /**
     * parametersProvider
     */
    public function parametersProvider()
    {
        $p1 = array(
            'asciiName'=>'London',
            'country'=>'GB',
            'orderBy'=>'distance',
            'lat'=>'0',
            'lng'=>'40'
        );

        $p2 = array(
            'asciiName'=>'London',
            'country'=>'GB'
        );

        $p3 = array(
            'asciiName'=>'Milano'
        );

        return array(
            array($p1),
            array($p2),
            array($p3)
        );
    }

    /**
     * testGetAllAction
     * @param array $parameters
     *
     * @dataProvider parametersProvider
     */
    public function testGetAllAction($parameters)
    {
        $client = static::createClient();

        $this->getRequest($client, 'api/geonames', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }


}