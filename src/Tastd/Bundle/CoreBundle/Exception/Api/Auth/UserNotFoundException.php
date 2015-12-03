<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UserNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class UserNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.auth.user_not_found')
    {
        $this->id = 'UserNotFound';
        $this->category = 'Auth';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'User not found.';
        $this->errors = null;
    }
}