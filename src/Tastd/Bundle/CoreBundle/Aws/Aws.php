<?php

namespace Tastd\Bundle\CoreBundle\Aws;
use Aws\Common\Aws as BaseAws;
use Aws\Common\Credentials\Credentials;

/**
 * Class S3Client
 *
 * @package Tastd\Bundle\CoreBundle\Aws
 */
class Aws
{
    /** @var Aws */
    protected $aws;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->aws = BaseAws::factory(array(
            'key' => $apiKey,
            'secret' => $apiSecret
        ));
    }

    /**
     * @param string $serviceName
     *
     * @return mixed
     */
    public function get($serviceName)
    {
        return $this->aws->get($serviceName);
    }
}