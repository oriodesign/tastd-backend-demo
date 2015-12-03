<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class AuthTokenExpiredException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class RefreshTokenExpiredException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'RefreshTokenExpired';
        $this->category = 'Auth';
        $this->code = 401;
        $this->message = 'exception.auth.refresh_token_expired';
        $this->info = 'The refresh token is expired. You need to login again.';
        $this->errors = null;
    }
}