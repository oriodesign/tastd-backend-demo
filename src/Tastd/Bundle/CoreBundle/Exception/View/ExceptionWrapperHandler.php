<?php

namespace Tastd\Bundle\CoreBundle\Exception\View;
use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;
use Tastd\Bundle\CoreBundle\Exception\Util\ExceptionWrapper;

/**
 * Class ExceptionWrapperHandler
 *
 * @package Tastd\Bundle\CoreBundle\Exception\View
 */
class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function wrap($data)
    {
        return new ExceptionWrapper($data);
    }
}
