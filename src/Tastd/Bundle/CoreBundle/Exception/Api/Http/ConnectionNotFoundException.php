<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class ConnectionNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class ConnectionNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.connection_not_found')
    {
        $this->id = 'ConnectionNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Connection not found.';
        $this->errors = null;
    }
}