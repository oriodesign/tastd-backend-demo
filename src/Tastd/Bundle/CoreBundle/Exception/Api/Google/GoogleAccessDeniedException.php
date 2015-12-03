<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Google;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class GoogleQuotaExceededException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class GoogleAccessDeniedException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'GoogleAccessDeniedException';
        $this->category = 'Google';
        $this->code = 403;
        $this->message = 'exception.google.google_access_denied';
        $this->info = 'Google answer with access denied. Maybe the api key is wrong.';
        $this->errors = null;
    }
}