<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class NotificationFactoryCompilerPass
 *
 * @package Tastd\Bundle\CoreBundle\DependencyInjection
 */
class NotificationFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tastd.notification_factory_bag')) {
            return;
        }

        $definition = $container->getDefinition(
            'tastd.notification_factory_bag'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'tastd.notification_factory'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addNotificationFactory',
                array(new Reference($id))
            );
        }
    }
}