<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class BadRequestException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class BadRequestException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct($errors = array(), $message = 'exception.http.bad_request', $info = 'Bad Request Exception')
    {
        $this->id = 'BadRequest';
        $this->category = 'Http';
        $this->code = 400;
        $this->message = $message;
        $this->info = $info;
        $this->errors = $errors;
    }
}