<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class CredentialNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class CredentialNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.auth.credential_not_found')
    {
        $this->id = 'CredentialNotFound';
        $this->category = 'Auth';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Credential not found.';
        $this->errors = null;
    }
}