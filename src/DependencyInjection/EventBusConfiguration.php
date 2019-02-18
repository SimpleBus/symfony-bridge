<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class EventBusConfiguration implements ConfigurationInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this->alias);

        if (method_exists($treeBuilder, 'getRootNode')) {
            // Symfony 4.2 +
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Symfony 4.1 and below
            $rootNode = $treeBuilder->root($this->alias);
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('event_name_resolver_strategy')
                    ->values(['class_based', 'named_message'])
                    ->defaultValue('class_based')
                ->end()
                ->arrayNode('logging')
                    ->canBeEnabled()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
