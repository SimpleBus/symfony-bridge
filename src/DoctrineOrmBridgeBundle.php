<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\AddMiddlewareTags;
use SimpleBus\SymfonyBridge\DependencyInjection\DoctrineOrmBridgeExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineOrmBridgeBundle extends Bundle
{
    use RequiresOtherBundles;

    private $configurationAlias;

    public function __construct($configurationAlias = 'doctrine_orm_bridge')
    {
        $this->configurationAlias = $configurationAlias;
    }

    public function getContainerExtension()
    {
        return new DoctrineOrmBridgeExtension($this->configurationAlias);
    }

    public function build(ContainerBuilder $container)
    {
        $this->checkRequirements(array('SimpleBusCommandBusBundle', 'SimpleBusEventBusBundle'), $container);

        $this->checkProxyManagerBridgeIsPresent();

        $container->addCompilerPass(
            new AddMiddlewareTags(
                'simple_bus.doctrine_orm_bridge.wraps_next_command_in_transaction',
                ['command'],
                100
            ),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            150
        );
    }

    private function checkProxyManagerBridgeIsPresent()
    {
        if (!class_exists('Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator')) {
            throw new \LogicException(
                sprintf(
                    'In order to use bundle "%s" you need to require "%s" package.',
                    $this->getName(),
                    'symfony/proxy-manager-bridge'
                )
            );
        }
    }
}
