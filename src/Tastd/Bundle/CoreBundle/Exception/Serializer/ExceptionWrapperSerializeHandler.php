<?php

namespace Tastd\Bundle\CoreBundle\Exception\Serializer;


use FOS\RestBundle\Util\ExceptionWrapper;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use FOS\RestBundle\Serializer\ExceptionWrapperSerializeHandler as BaseExceptionWrapperSerializeHandler;



/**
 * Class ExceptionWrapperSerializeHandler
 *
 * @package Tastd\Bundle\CoreBundle\Exception\Serializer
 */
class ExceptionWrapperSerializeHandler extends BaseExceptionWrapperSerializeHandler
{
    /**
     * @param ExceptionWrapper $exceptionWrapper
     *
     * @return array
     */
    protected function convertToArray(ExceptionWrapper $exceptionWrapper)
    {
        return array(
            'id' => $exceptionWrapper->getId(),
            'category' => $exceptionWrapper->getCategory(),
            'code' => $exceptionWrapper->getCode(),
            'message' => $exceptionWrapper->getMessage(),
            'info'=> $exceptionWrapper->getInfo(),
            'errors' => $exceptionWrapper->getErrors(),
        );
    }
}
