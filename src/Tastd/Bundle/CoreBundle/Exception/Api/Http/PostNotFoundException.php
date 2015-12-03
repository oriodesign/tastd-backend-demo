<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class PostNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class PostNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.post_not_found')
    {
        $this->id = 'PostNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Post not found.';
        $this->errors = null;
    }
}