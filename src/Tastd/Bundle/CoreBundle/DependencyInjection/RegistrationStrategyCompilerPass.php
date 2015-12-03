<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegistrationStrategyCompilerPass
 *
 * @package Tastd\Bundle\CoreBundle\DependencyInjection
 */
class RegistrationStrategyCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tastd.registration_strategy_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'tastd.registration_strategy_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'tastd.registration_strategy'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addRegistrationStrategy',
                array(new Reference($id))
            );
        }
    }
}