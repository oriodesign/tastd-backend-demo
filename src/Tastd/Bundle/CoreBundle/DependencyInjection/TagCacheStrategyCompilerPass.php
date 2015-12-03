<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TagCacheStrategyCompilerPass
 *
 * @package Tastd\Bundle\CoreBundle\DependencyInjection
 */
class TagCacheStrategyCompilerPass implements CompilerPassInterface
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
            'tastd.tag_cache_strategy'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addTagCacheStrategy',
                array(new Reference($id))
            );
        }
    }
}