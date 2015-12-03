<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class AlreadyRegisteredWithEmailException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class AlreadyRegisteredWithEmailException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.auth.already_registered_with_email')
    {
        $this->id = 'AlreadyRegisteredWithEmailException';
        $this->category = 'Auth';
        $this->code = 400;
        $this->message = $message;
        $this->info = 'The user is already registered with email and password.';
        $this->errors = null;
    }
}