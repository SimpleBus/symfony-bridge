<?php

namespace SimpleBus\SymfonyBridge;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

trait RequiresOtherBundles
{
    protected function checkRequirements(array $requiredBundles, ContainerBuilder $container)
    {
        if (!($this instanceof Bundle)) {
            throw new \LogicException('You can only use this trait with Bundle instances');
        }

        $enabledBundles = $container->getParameter('kernel.bundles');

        foreach ($requiredBundles as $requiredBundle) {
            if (!isset($enabledBundles[$requiredBundle])) {
                throw new \LogicException(sprintf('In order to use bundle "%s" you also need to enable "%s"', $this->getName(), $requiredBundle));
            }
        }
    }
}
