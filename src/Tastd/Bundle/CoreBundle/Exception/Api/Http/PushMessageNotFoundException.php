<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class PushMessageNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class PushMessageNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.push_message_not_found')
    {
        $this->id = 'PushMessageNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'PushMessage not found.';
        $this->errors = null;
    }
}