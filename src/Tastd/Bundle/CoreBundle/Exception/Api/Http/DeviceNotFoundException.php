<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class DeviceNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class DeviceNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.device_not_found')
    {
        $this->id = 'DeviceNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Device not found.';
        $this->errors = null;
    }
}