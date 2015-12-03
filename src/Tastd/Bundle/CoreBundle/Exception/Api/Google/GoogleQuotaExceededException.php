<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Google;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class GoogleQuotaExceededException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class GoogleQuotaExceededException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'GoogleQuotaExceeded';
        $this->category = 'Google';
        $this->code = 400;
        $this->message = 'exception.google.google_quota_exceeded';
        $this->info = 'Increase the maximum number of request per day to google api.';
        $this->errors = null;
    }
}