<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UserNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class NonUniqueUserException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.auth.non_unique_user')
    {
        $this->id = 'NonUniqueUser';
        $this->category = 'Auth';
        $this->code = 403;
        $this->message = $message;
        $this->info = 'User non unique.';
        $this->errors = null;
    }
}