<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigureMiddlewares implements CompilerPassInterface
{
    private $mainBusId;
    private $busTag;

    public function __construct($mainBusId, $busTag)
    {
        $this->mainBusId = $mainBusId;
        $this->busTag = $busTag;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->mainBusId)) {
            return;
        }

        $middlewareIds = new \SplPriorityQueue();

        foreach ($container->findTaggedServiceIds($this->busTag) as $specializedBusId => $tags) {
            foreach ($tags as $tagAttributes) {
                $priority = isset($tagAttributes['priority']) ? $tagAttributes['priority'] : 0;
                $middlewareIds->insert($specializedBusId, $priority);
            }
        }

        $orderedMiddlewareIds = iterator_to_array($middlewareIds, false);

        $mainBusDefinition = $container->findDefinition($this->mainBusId);
        foreach ($orderedMiddlewareIds as $middlewareId) {
            $mainBusDefinition->addMethodCall('appendMiddleware', [new Reference($middlewareId)]);
        }
    }
}
