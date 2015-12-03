<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Key\CredentialProvider;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;


/**
 * Class AuthControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class AuthControllerTest extends RestControllerTestCase
{
    /**
     * testMeAction
     */
    public function testLoginAction()
    {
        self::resetDb();
        $data = array(
            'email'=>'user@gmail.com',
            'provider'=> CredentialProvider::EMAIL,
            'password'=> 'user_password'
        );
        $client = static::createClient();
        $this->postRequest($client, 'auth/login', AuthToken::CORRECT, $data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testMeAction
     */
    public function testMeAction()
    {
        self::resetDb();
        $client = static::createClient();
        $this->getRequest($client, 'auth/me', AuthToken::CORRECT);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testMeAction
     */
    public function testWrongMeAction()
    {
        self::resetDb();
        $client = static::createClient();
        $this->getRequest($client, 'auth/me', AuthToken::WRONG);

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * testRegisterAction
     */
    public function testRegisterAction()
    {
        $client = static::createClient();
        $data = array(
                'provider' => 'EMAIL',
                'email' => 'test@email.com',
                'password' => 'john',
                'lastName' => 'john',
                'firstName' => 'Doe',
                'geoname' => 1
        );

        $this->postRequest($client, 'auth/register', AuthToken::NONE, $data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertDataValueEquals(array('user','email'), 'test@email.com', $client->getResponse()->getContent());
    }

    /**
     * testRefreshAction
     */
    public function testRefreshAction()
    {
        $client = static::createClient();
        $this->postRequest($client, 'auth/refresh?refresh-token='.RefreshToken::CORRECT, AuthToken::NONE);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}