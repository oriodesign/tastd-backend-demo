<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TastdCoreExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('tastd_core.client_config', $config['client']);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin.xml');
        $loader->load('aws.xml');
        $loader->load('cache.xml');
        $loader->load('commands.xml');
        $loader->load('controllers.xml');
        $loader->load('facebook.xml');
        $loader->load('factories.xml');
        $loader->load('google.xml');
        $loader->load('formatters.xml');
        $loader->load('foursquare.xml');
        $loader->load('listener.xml');
        $loader->load('managers.xml');
        $loader->load('repository.xml');
        $loader->load('services.xml');
        $loader->load('security.xml');
        $loader->load('validator.xml');
        $loader->load('voter.xml');
        $loader->load('notification.xml');

    }


}
