<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;

/**
 * Class ReviewControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class ReviewControllerTest extends RestControllerTestCase
{

    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass()
    {
        self::resetDb();
    }

    /**
     * getActionTest
     */
    public function testGetAction()
    {
        $client = static::createClient();
        $this->getRequest($client, 'api/reviews/1', AuthToken::CORRECT);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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
        $this->getRequest($client, 'api/reviews', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testNewAction
     */
    public function testNewAction()
    {
        $client = static::createClient();
        $data = array(
            "cost" => 30,
            "restaurant" => array(
                'id' => 4
            ),
            'cuisine' => array(
                'id' => 4
            ),
            'geoname' => array(
                'id' => 6
            ),
            'comment' => 'Comment Sample',
            'tags' => array(
                array(
                    'id' => 1
                ),
                array(
                    'id' => 2
                )
            )
        );

        $this->postRequest($client, 'api/reviews', AuthToken::CORRECT, $data);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * testUpdateAction
     */
    public function testUpdateAction()
    {
        $client = static::createClient();
        $data = array(
            "id" => 1,
            "cost" => 50,
            'comment' => "Sample comment update"
        );

        $this->putRequest($client, 'api/reviews/1', AuthToken::CORRECT, $data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }


    /**
     * testReorderAction
     */
    public function testReorderAction()
    {
        $client = static::createClient();
        $data = array(
            "reviews" => array(
                array(
                    "id" => 1,
                    "position" =>2
                ),
                array(
                    "id"=>2,
                    "position"=> 1
                ),
                array(
                    "id"=>3,
                    "position"=> 3
                )
            )
        );
        $this->postRequest($client, 'api/reviews/reorder', AuthToken::CORRECT, $data);
        $responseData = $this->getArrayFromClientResponse($client);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(2, $responseData['reviews'][0]['position']);
        $this->assertEquals(1, $responseData['reviews'][1]['position']);
        $this->assertEquals(3, $responseData['reviews'][2]['position']);
    }

    /**
     * testDeleteAction
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $this->deleteRequest($client, 'api/reviews/1', AuthToken::CORRECT);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }


}