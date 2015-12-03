<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class InvalidRegistrationTypeException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class InvalidRegistrationTypeException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'InvalidRegistrationType';
        $this->category = 'Auth';
        $this->code = 400;
        $this->message = 'exception.auth.invalid_registration_type';
        $this->info = 'Check your submitted registration type. Allowed registration types are EMAIL and FACEBOOK.';
        $this->errors = null;
    }
}