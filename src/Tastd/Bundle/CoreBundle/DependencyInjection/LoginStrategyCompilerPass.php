<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class LoginStrategyCompilerPass
 *
 * @package Tastd\Bundle\CoreBundle\DependencyInjection
 */
class LoginStrategyCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tastd.login_strategy_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'tastd.login_strategy_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'tastd.login_strategy'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addLoginStrategy',
                array(new Reference($id))
            );
        }
    }
}