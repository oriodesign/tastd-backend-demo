<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class InvalidCredentialException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class InvalidCredentialException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct($message = 'exception.auth.invalid_credential')
    {
        $this->id = 'InvalidCredential';
        $this->category = 'Auth';
        $this->code = 401;
        $this->message = $message;
        $this->info = 'There is no user associated to this credential token.';
        $this->errors = null;
    }
}