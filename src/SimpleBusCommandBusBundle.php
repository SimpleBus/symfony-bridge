<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\CommandBusExtension;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusCommandBusBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new ConfigureMiddlewares(
                'command_bus',
                'command_bus_middleware'
            )
        );

        $container->addCompilerPass(
            new RegisterHandlers(
                'simple_bus.command_bus.command_handler_map',
                'command_handler',
                'handles',
                false
            )
        );
    }

    public function getContainerExtension()
    {
        return new CommandBusExtension();
    }
}
