<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\CommandBusExtension;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterLoggingMiddleware;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusCommandBusBundle extends Bundle
{
    private $configurationAlias;

    public function __construct($alias = 'command_bus')
    {
        $this->configurationAlias = $alias;
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new RegisterLoggingMiddleware(
                'simple_bus.command_bus.logging_middleware',
                'simple_bus.command_bus.logging.enabled',
                'simple_bus.command_bus.logging.channel',
                'command_bus_middleware'
            )
        );

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
                'handles'
            )
        );
    }

    public function getContainerExtension()
    {
        return new CommandBusExtension($this->configurationAlias);
    }
}
