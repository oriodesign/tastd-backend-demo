<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Auth;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UserDisabledException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class UserDisabledException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'UserDisabled';
        $this->category = 'Auth';
        $this->code = 403;
        $this->message = 'exception.auth.user_disabled';
        $this->info = 'The user is disabled probably because he need to check email to activate the account.';
        $this->errors = null;
    }
}