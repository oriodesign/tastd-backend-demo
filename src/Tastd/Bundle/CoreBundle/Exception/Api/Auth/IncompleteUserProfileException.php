<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UserLockedException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class IncompleteUserProfileException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'IncompleteUserProfileException';
        $this->category = 'Auth';
        $this->code = 403;
        $this->message = 'exception.auth.incomplete_user_profile';
        $this->info = 'First name and last name are required before proceed.';
        $this->errors = null;
    }
}