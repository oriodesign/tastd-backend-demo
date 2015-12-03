<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class OptionNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class OptionNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.option_not_found')
    {
        $this->id = 'OptionNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Option not found.';
        $this->errors = null;
    }
}