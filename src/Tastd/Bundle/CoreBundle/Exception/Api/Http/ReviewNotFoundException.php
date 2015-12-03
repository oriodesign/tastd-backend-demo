<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class ReviewNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class ReviewNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.review_not_found')
    {
        $this->id = 'ReviewNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Review not found.';
        $this->errors = null;
    }
}