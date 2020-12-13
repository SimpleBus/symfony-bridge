<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class EventBusExtension extends ConfigurableExtension
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
        return new EventBusConfiguration($this->getAlias());
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('event_bus.yml');

        $container->setAlias(
            'simple_bus.event_bus.event_name_resolver',
            'simple_bus.event_bus.'.$mergedConfig['event_name_resolver_strategy'].'_event_name_resolver'
        );

        if ($mergedConfig['logging']['enabled']) {
            $loader->load('event_bus_logging.yml');

            $container->getDefinition('simple_bus.event_bus.notifies_message_subscribers_middleware')
                ->replaceArgument(1, new Reference('logger'))
                ->replaceArgument(2, '%simple_bus.event_bus.logging.level%')
                ->addTag('monolog.logger', ['channel' => 'event_bus'])
            ;
        }
    }
}
