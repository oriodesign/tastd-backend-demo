<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;
use Tastd\Bundle\CoreBundle\Key\Header;

/**
 * Class MissingAuthTokenException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class MissingAuthTokenException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->category = 'Auth';
        $this->id = 'MissingAuthToken';
        $this->code = 401;
        $this->message = 'exception.auth.missing_auth_token';
        $this->info = 'Auth token expected to be set. http header is missing or value is null';
        $this->errors = null;
    }
}