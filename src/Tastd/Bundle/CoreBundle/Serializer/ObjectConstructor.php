<?php

namespace Tastd\Bundle\CoreBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\DeserializationContext;

/**
 * Class ObjectConstructor
 *
 * @package Tastd\Bundle\CoreBundle\ApiSerializer
 */
class ObjectConstructor implements ObjectConstructorInterface
{
    /**
     * @param VisitorInterface       $visitor
     * @param ClassMetadata          $metadata
     * @param mixed                  $data
     * @param array                  $type
     * @param DeserializationContext $context
     * @return object
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        $className = $metadata->name;

        return new $className();
    }
}