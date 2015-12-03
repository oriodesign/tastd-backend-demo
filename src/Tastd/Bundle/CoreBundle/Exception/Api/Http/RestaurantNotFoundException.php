<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class ConnectionNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class RestaurantNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.restaurant_not_found')
    {
        $this->id = 'RestaurantNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Restaurant not found.';
        $this->errors = null;
    }
}