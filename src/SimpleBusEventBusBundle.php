<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterMessageRecorders;
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
            new RegisterMessageRecorders(
                'simple_bus.event_bus.aggregates_recorded_messages',
                'message_recorder'
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
