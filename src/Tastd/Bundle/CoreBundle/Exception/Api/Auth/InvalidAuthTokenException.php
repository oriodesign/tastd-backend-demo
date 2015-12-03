<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class InvalidAuthTokenException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class InvalidAuthTokenException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'InvalidAuthToken';
        $this->category = 'Auth';
        $this->code = 401;
        $this->message = 'exception.auth.invalid_auth_token';
        $this->info = 'There is no user associated to this auth token.';
        $this->errors = null;
    }
}