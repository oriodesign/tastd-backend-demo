<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;


/**
 * Class UserCredentialControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class UserFollowersControllerTest extends RestControllerTestCase
{
    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass()
    {
        self::resetDb();
    }


    /**
     * testUpdateAction
     */
    public function testCreateAction()
    {
        $client = static::createClient();
        $data = array("id"=>1);
        $this->postRequest($client, 'api/users/15/followers', AuthToken::CORRECT, $data);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * testUpdateAction
     */
    public function testUpdateAction()
    {
        $client = static::createClient();
        $this->putRequest($client, 'api/users/15/followers/1?status=REFUSED', AuthToken::CORRECT);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testDeleteAction
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $this->deleteRequest($client, 'api/users/15/followers/1', AuthToken::CORRECT);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

}