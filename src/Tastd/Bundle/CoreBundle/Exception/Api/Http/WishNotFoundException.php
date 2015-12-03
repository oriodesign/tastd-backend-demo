<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class WishNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class WishNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.wish_not_found')
    {
        $this->id = 'WishNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Wish not found.';
        $this->errors = null;
    }
}