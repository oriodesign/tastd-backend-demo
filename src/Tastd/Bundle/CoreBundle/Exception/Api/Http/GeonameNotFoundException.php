<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Http;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class GeonameNotFoundException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class GeonameNotFoundException extends AbstractApiException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'exception.http.geoname_not_found')
    {
        $this->id = 'GeonameNotFound';
        $this->category = 'Http';
        $this->code = 404;
        $this->message = $message;
        $this->info = 'Geoname not found.';
        $this->errors = null;
    }
}