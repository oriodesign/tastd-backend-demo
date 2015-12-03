<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Cache;

use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class UndefinedTagException
 *
 * @package Tastd\Bundle\CoreBundle\Exception\Api\Cache
 */
class UndefinedTagException extends AbstractApiException
{

    public function __construct()
    {
        $this->id = 'UndefinedTagException';
        $this->category = 'Cache';
        $this->code = 404;
        $this->message = 'Ooops... We have a problem houston.';
        $this->info = 'Impossible generate cache tag for this entity.';
        $this->errors = null;
    }
}