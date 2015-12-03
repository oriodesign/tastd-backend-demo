<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class AccessDeniedException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class AccessDeniedException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'AccessDenied';
        $this->category = 'Http';
        $this->code = 403;
        $this->message = 'exception.http.access_denied';
        $this->info = 'Access denied.';
        $this->errors = null;
    }
}