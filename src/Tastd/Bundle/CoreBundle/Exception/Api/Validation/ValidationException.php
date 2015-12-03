<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Validation;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class ValidationException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class ValidationException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct($errors = array())
    {
        $this->id = 'Validation';
        $this->category = 'Validation';
        $this->code = 400;
        $this->message = 'exception.validation.validation';
        $this->info = 'Validation exception.';
        $this->errors = $errors;
    }
}