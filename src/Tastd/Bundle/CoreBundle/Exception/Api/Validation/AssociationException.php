<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Validation;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class AssociationException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class AssociationException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'Association';
        $this->category = 'Validation';
        $this->code = 400;
        $this->message = 'exception.validation.association';
        $this->info = 'You call a url like /user/1/address/1 but they are not associated.';
        $this->errors = null;
    }
}