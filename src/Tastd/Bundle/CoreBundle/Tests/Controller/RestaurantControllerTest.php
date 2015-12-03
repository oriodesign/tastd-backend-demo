<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;


/**
 * Class RestaurantControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class RestaurantControllerTest extends RestControllerTestCase
{
    /**
     * testGetAllAction
     */
    public function testGetAllAction()
    {
        /**
         * For case insensitive search remember to set utf8_general_ci
         * as your default collate in mysql db
         */
        self::resetDb();
        $parameters = array(
            'page' => 1,
            'geoname' => 6,
            'name' => 'Daniel'
        );
        $client = static::createClient();
        $this->getRequest($client, 'api/restaurants', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertDataValueLengthGreater(array('restaurants'), 0, $client->getResponse()->getContent());
    }

    /**
     * testNewAction
     */
    public function testNewAction()
    {
        $client = static::createClient();
        $data = array(
            "name" => 'Il re del Kebab',
            "cuisine" => 1,
            'address'=> 'Milan, via Rossi 12',
            'lat'=>'45.4642700',
            'lng'=>'9.1895100',
            'geoname' => array(
                'id' => 8
            )
        );
        $this->postRequest($client, 'api/restaurants', AuthToken::CORRECT, $data);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * testUpdateAction
     */
    public function testUpdateAction()
    {
        $client = static::createClient();
        $data = array(
            "id"=> 1,
            "name" => 'XXX',
            "cuisine" => 2,
            'address'=> 'asd',
            'lat'=>'45.4642700',
            'lng'=>'9.1895100',
            'geoname' => array(
                'id' => 8
            )
        );
        $this->putRequest($client, 'api/restaurants/1', AuthToken::CORRECT, $data);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * testGetAction
     */
    public function testGetAction()
    {
        $client = static::createClient();
        $this->getRequest($client, 'api/restaurants/1', AuthToken::CORRECT);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}