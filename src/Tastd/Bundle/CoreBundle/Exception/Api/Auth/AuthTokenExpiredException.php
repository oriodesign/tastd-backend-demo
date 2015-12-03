<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class AuthTokenExpiredException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class AuthTokenExpiredException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'AuthTokenExpired';
        $this->category = 'Auth';
        $this->code = 401;
        $this->message = 'exception.auth.auth_token_expired';
        $this->info = 'The auth token expire after 1 hour. Use the refresh token to take a valid auth token.';
        $this->errors = null;
    }
}