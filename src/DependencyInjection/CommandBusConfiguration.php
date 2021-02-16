<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CommandBusConfiguration implements ConfigurationInterface
{
    private string $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('command_name_resolver_strategy')
                    ->values(['class_based', 'named_message'])
                    ->defaultValue('class_based')
                ->end()
                ->arrayNode('middlewares')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('logger')->defaultFalse()->end()
                        ->booleanNode('finishes_command_before_handling_next')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('logging')
                    ->canBeEnabled()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
