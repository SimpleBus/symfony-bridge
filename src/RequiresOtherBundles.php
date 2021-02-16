<?php

namespace SimpleBus\SymfonyBridge;

use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

trait RequiresOtherBundles
{
    /**
     * @param string[] $requiredBundles
     */
    protected function checkRequirements(array $requiredBundles, ContainerBuilder $container): void
    {
        if (!($this instanceof Bundle)) {
            throw new LogicException('You can only use this trait with Bundle instances');
        }

        $enabledBundles = (array) $container->getParameter('kernel.bundles');

        foreach ($requiredBundles as $requiredBundle) {
            if (!isset($enabledBundles[$requiredBundle])) {
                throw new LogicException(sprintf('In order to use bundle "%s" you also need to enable "%s"', $this->getName(), $requiredBundle));
            }
        }
    }
}
