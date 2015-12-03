<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class PhotoNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class PhotoNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.photo_not_found')
    {
        $this->id = 'PhotoNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Photo not found.';
        $this->errors = null;
    }
}