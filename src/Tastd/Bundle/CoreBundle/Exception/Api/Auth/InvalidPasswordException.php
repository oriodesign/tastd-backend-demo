<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class InvalidPasswordException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class InvalidPasswordException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'InvalidPassword';
        $this->category = 'Auth';
        $this->code = 400;
        $this->message = 'exception.auth.invalid_password';
        $this->info = 'The password is wrong. Suggest to recover it with email.';
        $this->errors = null;
    }
}