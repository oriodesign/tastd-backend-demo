<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;

/**
 * Class WishControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class WishControllerTest extends RestControllerTestCase
{

    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass()
    {
        self::resetDb();
    }

    /**
     * testGetAllAction
     */
    public function testGetAllAction()
    {
        $parameters = array(
            'cuisine' => 1,
            'geoname' => 1,
            'user' => 1
        );
        $client = static::createClient();
        $this->getRequest($client, 'api/wishes', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testNewAction
     */
    public function testNewAction()
    {
        $client = static::createClient();
        $data = array(
            "restaurant" => array(
                'id' => 4
            ),
            'cuisine' => array(
                'id' => 4
            ),
            'geoname' => array(
                'id' => 6
            )
        );

        $this->postRequest($client, 'api/wishes', AuthToken::CORRECT, $data);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * testDeleteAction
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $this->deleteRequest($client, 'api/wishes/1', AuthToken::CORRECT);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

}