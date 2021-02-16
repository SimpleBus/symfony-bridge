<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DoctrineOrmBridgeExtension extends ConfigurableExtension
{
    private string $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param mixed[] $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): DoctrineOrmBridgeConfiguration
    {
        return new DoctrineOrmBridgeConfiguration($this->getAlias());
    }

    /**
     * @param mixed[] $mergedConfig
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

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
