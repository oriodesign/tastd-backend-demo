<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UserExpiredException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class UserExpiredException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'UserExpired';
        $this->category = 'Auth';
        $this->code = 403;
        $this->message = 'exception.auth.user_expired';
        $this->info = 'This functionality is disabled in Tastd. So if you see this exception there is an error.';
        $this->errors = null;
    }
}