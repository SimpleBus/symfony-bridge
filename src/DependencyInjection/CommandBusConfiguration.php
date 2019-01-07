<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CommandBusConfiguration implements ConfigurationInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this->alias);

        $rootNode = $treeBuilder->root($this->alias);
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('command_name_resolver_strategy')
                    ->values(['class_based', 'named_message'])
                    ->defaultValue('class_based')
                ->end()
                ->booleanNode('unnest_commands')
                    ->info('Handle commands sequentially')
                    ->defaultTrue()
                ->end()
                ->arrayNode('logging')
                    ->canBeEnabled()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
