<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EntityFormatterFactoryCompilerPass
 *
 * @package Tastd\Bundle\CoreBundle\DependencyInjection
 */
class EntityFormatterCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tastd.entity_formatter_bag')) {
            return;
        }

        $definition = $container->getDefinition(
            'tastd.entity_formatter_bag'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'tastd.entity_formatter'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addEntityFormatter',
                array(new Reference($id))
            );
        }
    }
}