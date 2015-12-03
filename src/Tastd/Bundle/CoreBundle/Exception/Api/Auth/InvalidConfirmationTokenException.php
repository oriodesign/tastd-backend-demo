<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class InvalidConfirmationTokenException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class InvalidConfirmationTokenException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'InvalidConfirmationToken';
        $this->category = 'Auth';
        $this->code = 401;
        $this->message = 'exception.auth.invalid_confirmation_token';
        $this->info = 'There is no user associated to this confirmation token.';
        $this->errors = null;
    }
}