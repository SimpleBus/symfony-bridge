<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureBuses;
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
            new ConfigureBuses(
                'event_bus',
                'event_bus'
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
                'simple_bus.event_bus.event_handlers_resolver',
                'event_handler',
                'handles',
                true
            )
        );
    }

    public function getContainerExtension()
    {
        return new EventBusExtension();
    }
}
