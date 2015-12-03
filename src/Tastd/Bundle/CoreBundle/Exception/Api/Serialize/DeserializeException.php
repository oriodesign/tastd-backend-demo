<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api\Serialize;
use Tastd\Bundle\CoreBundle\Exception\Api\AbstractApiException;

/**
 * Class DeserializeException
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
class DeserializeException extends AbstractApiException
{
    /**
     * __construct
     */
    public function __construct()
    {
        $this->id = 'Deserialize';
        $this->category = 'Serialize';
        $this->code = 400;
        $this->message = 'exception.serialize.deserialize';
        $this->info = 'A problem occurred during object de-serialization.';
        $this->errors = null;
    }
}