<?php

namespace Tastd\Bundle\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tastd\Bundle\CoreBundle\DependencyInjection\EntityFormatterCompilerPass;
use Tastd\Bundle\CoreBundle\DependencyInjection\InvalidateCacheStrategyCompilerPass;
use Tastd\Bundle\CoreBundle\DependencyInjection\LoginStrategyCompilerPass;
use Tastd\Bundle\CoreBundle\DependencyInjection\NotificationFactoryCompilerPass;
use Tastd\Bundle\CoreBundle\DependencyInjection\RegistrationStrategyCompilerPass;
use Tastd\Bundle\CoreBundle\DependencyInjection\TagCacheStrategyCompilerPass;
use Tastd\Bundle\CoreBundle\DependencyInjection\ValidationCompilerPass;

/**
 * Class TastdCoreBundle
 *
 * @package Tastd\Bundle\CoreBundle
 */
class TastdCoreBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoginStrategyCompilerPass());
        $container->addCompilerPass(new RegistrationStrategyCompilerPass());
        $container->addCompilerPass(new NotificationFactoryCompilerPass());
        $container->addCompilerPass(new ValidationCompilerPass());
        $container->addCompilerPass(new EntityFormatterCompilerPass());
        $container->addCompilerPass(new TagCacheStrategyCompilerPass());
        $container->addCompilerPass(new InvalidateCacheStrategyCompilerPass());
        $container->addCompilerPass(new EntityFormatterCompilerPass());
    }

}
