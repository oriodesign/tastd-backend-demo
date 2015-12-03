<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Geo;

use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UnresolvedGeonameException
 *
 * @package Tastd\Bundle\CoreBundle\Exception\Api\Geo
 */
class UnresolvedGeonameException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'GeonameNotFound';
        $this->category = 'Geo';
        $this->code = 400;
        $this->message = 'exception.geo.unresolved_geoname';
        $this->info = 'Impossible find an unique geoname for this request. Ask the user.';
        $this->errors = null;
    }
}