<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Google;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class InvalidGoogleCredentialException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class InvalidGoogleCredentialException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'InvalidGoogleCredential';
        $this->category = 'Google';
        $this->code = 400;
        $this->message = 'exception.google.invalid_google_credential';
        $this->info = 'The api key is wrong.';
        $this->errors = null;
    }
}