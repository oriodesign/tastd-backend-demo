<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class InvalidRefreshTokenException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class InvalidRefreshTokenException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'InvalidRefreshToken';
        $this->category = 'Auth';
        $this->code = 401;
        $this->message = 'exception.auth.invalid_refresh_token';
        $this->info = 'There is no user associated to this refresh token.';
        $this->errors = null;
    }
}