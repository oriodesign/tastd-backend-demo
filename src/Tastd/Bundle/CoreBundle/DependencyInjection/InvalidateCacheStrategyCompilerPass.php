<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class InvalidateCacheStrategyCompilerPass
 *
 * @package Tastd\Bundle\CoreBundle\DependencyInjection
 */
class InvalidateCacheStrategyCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tastd.cache_manager')) {
            return;
        }

        $definition = $container->getDefinition(
            'tastd.cache_manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'tastd.invalidate_cache_strategy'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addInvalidateCacheStrategy',
                array(new Reference($id))
            );
        }
    }
}