<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\CommandBusExtension;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\AutoRegister;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterHandlers;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
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
            new AutoRegister('command_handler', 'handles'),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            10
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
                'simple_bus.command_bus.command_handler_service_locator',
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
