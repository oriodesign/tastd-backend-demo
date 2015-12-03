<?php

namespace Tastd\Bundle\CoreBundle\Tests\Controller;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Client;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tastd\Bundle\CoreBundle\Key\Header;

/**
 * Class RestControllerTestCase
 *
 * @package Tastd\Bundle\CoreBundle\Tests\Controller
 */
class RestControllerTestCase extends WebTestCase
{
    /**
     * loadFixtures
     */
    public static function resetDb()
    {
        exec('cd '.__DIR__.'/../../../../../../app && ls && php console apperclass:fixture:import --env=test');
    }

    /**
     * @param Client $client
     * @param string $uri
     * @param string $token
     * @param array  $parameters
     * @param array  $headers
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function getRequest(Client $client, $uri, $token, $parameters = array(), $headers = array())
    {
        $defaultHeaders = array('HTTP_'.Header::AUTH_TOKEN=> $token,'HTTP_Accept'=>'application/json');
        $headers = array_merge($headers, $defaultHeaders);

        return $client->request(
            'GET',
            $uri,
            $parameters, //Parameters
            array(), //Files
            $headers,
            null, //Content
            true //Change History
        );
    }

    /**
     * @param Client $client
     * @param string $uri
     * @param string $token
     * @param array  $array
     * @param array  $files
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function postRequest(Client $client, $uri, $token, $array=array(), $files = array())
    {
        $content = json_encode($array);

        return $client->request(
            'POST',
            $uri,
            array(), //Parameters
            $files, //Files
            array(
                'HTTP_'.Header::AUTH_TOKEN=> $token,
                'HTTP_Accept'=>'application/json',
                'CONTENT_TYPE' => 'application/json'),
            $content, //Content
            true //Change History
        );
    }

    /**
     * @param Client $client
     * @param string $uri
     * @param string $token
     * @param array  $array
     * @param array  $files
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function putRequest(Client $client, $uri, $token, $array=array(), $files = array())
    {
        $content = json_encode($array);

        return $client->request(
            'PUT',
            $uri,
            array(), //Parameters
            $files, //Files
            array(
                'HTTP_'.Header::AUTH_TOKEN=> $token,
                'HTTP_Accept'=>'application/json',
                'CONTENT_TYPE' => 'application/json'),
            $content, //Content
            true //Change History
        );
    }

    /**
     * @param Client $client
     * @param string $uri
     * @param string $token
     * @param array  $array
     * @param array  $files
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function deleteRequest(Client $client, $uri, $token, $array=array(), $files = array())
    {
        $content = json_encode($array);

        return $client->request(
            'DELETE',
            $uri,
            array(), //Parameters
            $files, //Files
            array(
                'HTTP_'.Header::AUTH_TOKEN=> $token,
                'HTTP_Accept'=>'application/json',
                'CONTENT_TYPE' => 'application/json'),
            $content, //Content
            true //Change History
        );
    }

    /**
     * @param Client $client
     * @param string $uri
     * @param string $token
     * @param array  $array
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function patchRequest(Client $client, $uri, $token, $array=array())
    {
        $content = json_encode($array);

        return $client->request(
            'PATCH',
            $uri,
            array(), //Parameters
            array(), //Files
            array(
                'HTTP_'.Header::AUTH_TOKEN=> $token,
                'HTTP_Accept'=>'application/json',
                'CONTENT_TYPE' => 'application/json'),
            $content, //Content
            true //Change History
        );
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * Assert that response contains in that key that value
     * @param array $keys    - array('activity','id')
     * @param mixed $value   - the expected value
     * @param mixed $content - json or associative array
     */
    public function assertDataValueEquals($keys, $value, $content)
    {
        $data = is_string($content) ? json_decode($content, true) : $content;
        $currentKey = $keys[0];
        array_shift($keys);
        if (array_key_exists($currentKey, $data)) {
            $result = $data[$currentKey];
            if (count($keys) === 0) {
                $this->assertEquals($value, $result);
            } else {
                $this->assertDataValueEquals($keys, $value, $data[$currentKey]);
            }
        } else {
            $this->fail('Key ' . $currentKey . ' doesn\'t exists in response data: ' . implode(',', array_keys($data)));
        }
    }

    /**
     * Assert that response array length is
     * @param array $keys    - array('activity','id')
     * @param int   $value   - the expected value
     * @param mixed $content - json or associative array
     */
    public function assertDataValueLengthGreater($keys, $value, $content)
    {
        $data = is_string($content) ? json_decode($content, true) : $content;
        $currentKey = $keys[0];
        array_shift($keys);
        if (array_key_exists($currentKey, $data)) {
            $result = $data[$currentKey];
            if (count($keys) === 0) {
                $this->assertGreaterThan($value, count($result));
            } else {
                $this->assertDataValueLengthGreater($keys, $value, $data[$currentKey]);
            }
        } else {
            $this->fail('Key ' . $currentKey . ' doesn\'t exists in response data: ' . implode(',', array_keys($data)));
        }
    }

    /**
     * Assert that response array length is
     * @param array $keys    - array('activity','id')
     * @param int   $value   - the expected value
     * @param mixed $content - json or associative array
     */
    public function assertDataValueLengthEquals($keys, $value, $content)
    {
        $data = is_string($content) ? json_decode($content, true) : $content;
        $currentKey = $keys[0];
        array_shift($keys);
        if (array_key_exists($currentKey, $data)) {
            $result = $data[$currentKey];
            if (count($keys) === 0) {
                $this->assertEquals($value, count($result));
            } else {
                $this->assertDataValueLengthEquals($keys, $value, $data[$currentKey]);
            }
        } else {
            $this->fail('Key ' . $currentKey . ' doesn\'t exists in response data: ' . implode(',', array_keys($data)));
        }
    }

    /**
     * Assert that response array length is
     * @param array $keys    - array('activity','id')
     * @param int   $value   - the expected value
     * @param mixed $content - json or associative array
     */
    public function assertDataValueLengthLess($keys, $value, $content)
    {
        $data = is_string($content) ? json_decode($content, true) : $content;
        $currentKey = $keys[0];
        array_shift($keys);
        if (array_key_exists($currentKey, $data)) {
            $result = $data[$currentKey];
            if (count($keys) === 0) {
                $this->assertLessThan($value, count($result));
            } else {
                $this->assertDataValueLengthLess($keys, $value, $data[$currentKey]);
            }
        } else {
            $this->fail('Key ' . $currentKey . ' doesn\'t exists in response data: ' . implode(',', array_keys($data)));
        }
    }

    /**
     * @param Client $client
     * @param int    $length
     */
    public function log(Client $client, $length = 50)
    {
        $logger = $this->getLogger();
        $response = $client->getResponse()->getContent();
        $logger->info(substr($response, 0, $length));
    }

    /**
     * @param Client $client
     * @return mixed
     */
    public function getArrayFromClientResponse(Client $client)
    {
        return json_decode($client->getResponse()->getContent(), true);
    }


}