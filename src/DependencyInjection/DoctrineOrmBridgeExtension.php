<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class DoctrineOrmBridgeExtension extends ConfigurableExtension
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new DoctrineOrmBridgeConfiguration($this->getAlias());
    }

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('doctrine_orm_bridge.yml');

        $container->setParameter(
            'simple_bus.doctrine_orm_bridge.entity_manager',
            $mergedConfig['entity_manager']
        );

        $connection = $container->getParameterBag()->resolveValue($mergedConfig['connection']);
        $container
            ->findDefinition('simple_bus.doctrine_orm_bridge.collects_events_from_entities')
            ->addTag('doctrine.event_subscriber', ['connection' => $connection]);
    }
}
