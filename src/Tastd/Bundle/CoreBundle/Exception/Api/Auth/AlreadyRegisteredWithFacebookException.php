<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class AlreadyRegisteredWithFacebookException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class AlreadyRegisteredWithFacebookException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.auth.already_registered_with_facebook')
    {
        $this->id = 'AlreadyRegisteredWithEmailFacebook';
        $this->category = 'Auth';
        $this->code = 400;
        $this->message = $message;
        $this->info = 'The user is already registered with facebook.';
        $this->errors = null;
    }
}