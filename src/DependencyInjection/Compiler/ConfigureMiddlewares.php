<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use SplPriorityQueue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigureMiddlewares implements CompilerPassInterface
{
    private string $mainBusId;
    private string $busTag;

    public function __construct(string $mainBusId, string $busTag)
    {
        $this->mainBusId = $mainBusId;
        $this->busTag = $busTag;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->mainBusId)) {
            return;
        }

        $middlewareIds = new SplPriorityQueue();

        foreach ($container->findTaggedServiceIds($this->busTag) as $specializedBusId => $tags) {
            foreach ($tags as $tagAttributes) {
                $priority = $tagAttributes['priority'] ?? 0;
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
