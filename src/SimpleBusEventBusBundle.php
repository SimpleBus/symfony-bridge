<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterEventProviders;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use SimpleBus\SymfonyBridge\DependencyInjection\EventBusExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusEventBusBundle extends Bundle
{
    use RequiresOtherBundles;

    protected function requires()
    {
        return array('SimpleBusCommandBusBundle');
    }

    public function build(ContainerBuilder $container)
    {
        $this->checkRequirements(array('SimpleBusCommandBusBundle'), $container);

        $container->addCompilerPass(
            new ConfigureMiddlewares(
                'event_bus',
                'event_bus_middleware'
            )
        );

        $container->addCompilerPass(
            new RegisterEventProviders(
                'simple_bus.event_bus.aggregates_multiple_event_providers',
                'event_provider'
            )
        );

        $container->addCompilerPass(
            new RegisterHandlers(
                'simple_bus.event_bus.event_subscribers_collection',
                'event_subscriber',
                'subscribes_to',
                true
            )
        );
    }

    public function getContainerExtension()
    {
        return new EventBusExtension();
    }
}
