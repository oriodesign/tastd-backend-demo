<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UserLockedException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class UserLockedException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'UserLocked';
        $this->category = 'Auth';
        $this->code = 403;
        $this->message = 'exception.auth.user_locked';
        $this->info = 'The admin has manually locked this account.';
        $this->errors = null;
    }
}