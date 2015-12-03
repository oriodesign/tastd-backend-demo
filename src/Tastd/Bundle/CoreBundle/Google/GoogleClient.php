<?php

namespace Tastd\Bundle\CoreBundle\Google;
use Google_Client;

/**
 * Class GoogleClient
 *
 * @package Tastd\Bundle\CoreBundle\Google
 */
class GoogleClient
{
    protected $applicationName;
    protected $appKey;
    /** @var Google_Client */
    protected $client;

    /**
     * @param string $applicationName
     * @param string $appKey
     */
    public function __construct($applicationName, $appKey)
    {
        $this->applicationName = $applicationName;
        $this->appKey = $appKey;
    }

    /**
     * connect
     */
    public function connect()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName($this->applicationName);
        $this->client->setDeveloperKey($this->appKey);
    }



}