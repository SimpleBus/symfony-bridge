<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class DoctrineOrmBridgeConfiguration implements ConfigurationInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root($this->alias);

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('entity_manager')
                    ->defaultValue('default')
                ->end()
                ->scalarNode('connection')
                    ->defaultValue('default')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
