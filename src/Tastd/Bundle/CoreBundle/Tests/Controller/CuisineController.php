<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tastd\Bundle\CoreBundle\Tests\Key\AuthToken;
use Tastd\Bundle\CoreBundle\Tests\Key\RefreshToken;

/**
 * Class CuisineControllerTest
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class CuisineControllerTest extends RestControllerTestCase
{
    /**
     * testGetAll
     */
    public function testGetAll()
    {
        $client = static::createClient();

        $this->getRequest($client, 'api/cuisines', AuthToken::CORRECT);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}