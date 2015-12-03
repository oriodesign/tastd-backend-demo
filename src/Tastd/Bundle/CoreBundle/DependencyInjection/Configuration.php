<?php

namespace Tastd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tastd_core');

        $rootNode
           ->children()
                ->arrayNode('client')
                    ->children()
                        ->arrayNode('app')
                            ->children()
                                ->scalarNode('name')->defaultValue('Tastd')->end()
                            ->end() // children
                        ->end() // app
                        ->arrayNode('facebook')
                            ->children()
                                ->scalarNode('app_name')->defaultValue('Tastd')->end()
                                ->scalarNode('app_id')->end()
                                ->scalarNode('api_version')->defaultValue('v1')->end()
                                ->booleanNode('enabled')->defaultValue(true)->end()
                                ->arrayNode('app_permissions')
                                    ->children()
                                        ->arrayNode('login')->prototype('scalar')->end()
                                    ->end() // children
                                ->end() // permissions
                            ->end() // children
                        ->end() //facebook
                    ->end()// children
                ->end() // client
            ->end() // children
        ;

        return $treeBuilder;
    }
}
