<?php

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterTransactionalMiddleware;
use SimpleBus\SymfonyBridge\DependencyInjection\DoctrineOrmBridgeExtension;
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

        // hack, needed to prepend our compiler pass instead of appending it
        $compilerPassConfig = $container->getCompilerPassConfig();
        $beforeOptimizationPasses = $compilerPassConfig->getBeforeOptimizationPasses();
        array_unshift($beforeOptimizationPasses, new RegisterTransactionalMiddleware());
        $compilerPassConfig->setBeforeOptimizationPasses($beforeOptimizationPasses);
    }
}
