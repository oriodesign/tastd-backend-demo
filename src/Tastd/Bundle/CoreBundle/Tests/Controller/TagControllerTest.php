<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;

use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;

/**
 * Class TagControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class TagControllerTest extends RestControllerTestCase
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
            'highlight' => true
        );
        $client = static::createClient();
        $this->getRequest($client, 'api/tags', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testNewAction
     */
    public function testNewAction()
    {
        $client = static::createClient();
        $data = array(
            "name" => 'my fabulous tag',
            "groupId" => 1
        );

        $this->postRequest($client, 'api/tags', AuthToken::CORRECT, $data);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

}