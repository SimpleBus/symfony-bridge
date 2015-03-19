<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPassUtil
{
    public static function prependBeforeOptimizationPass(
        ContainerBuilder $container,
        CompilerPassInterface $compilerPass
    ) {
        $compilerPassConfig = $container->getCompilerPassConfig();
        $beforeOptimizationPasses = $compilerPassConfig->getBeforeOptimizationPasses();
        array_unshift($beforeOptimizationPasses, $compilerPass);
        $compilerPassConfig->setBeforeOptimizationPasses($beforeOptimizationPasses);
    }
}
