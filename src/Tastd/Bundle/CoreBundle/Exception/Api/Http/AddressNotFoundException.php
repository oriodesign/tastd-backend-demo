<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class AddressNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class AddressNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.address_not_found')
    {
        $this->id = 'AddressNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Address not found.';
        $this->errors = null;
    }
}