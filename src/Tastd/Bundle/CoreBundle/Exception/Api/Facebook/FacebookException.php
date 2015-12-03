<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Facebook;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class FacebookException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class FacebookException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.facebook.facebook')
    {
        $this->id = 'Facebook';
        $this->category = 'Facebook';
        $this->code = 400;
        $this->message = $message;
        $this->info = 'The server called the facebook api but it received an error. Double check your facebook token.';
        $this->errors = null;
    }
}