<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;


/**
 * Class PushMessageControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class PushMessageControllerTest extends RestControllerTestCase
{
    /**
     * testGetAllAction
     */
    public function testGetAllAction()
    {
        self::resetDb();
        $parameters = array(
            'page' => 1,
            'user' => 1
        );
        $client = static::createClient();
        $this->getRequest($client, 'api/push-messages', AuthToken::CORRECT, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testUpdateAction
     */
    public function testUpdateAction()
    {
        $client = static::createClient();
        $data = array(
            "id"=> 1,
            "seen" => true
        );
        $this->putRequest($client, 'api/push-messages/1', AuthToken::CORRECT, $data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testDeleteAction
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $this->deleteRequest($client, 'api/push-messages/1', AuthToken::CORRECT);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

}