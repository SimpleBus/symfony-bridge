<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class ProfilerPass implements CompilerPassInterface
{
    const MESSAGE_BUS_TAG = 'message_bus';

    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds(self::MESSAGE_BUS_TAG) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                $busName = $this->getAttribute($tagAttributes, 'bus_name', self::MESSAGE_BUS_TAG, $serviceId);

                $busRegistry = $container->getDefinition('simple_bus.profiler.bus_registry');
                $busRegistry->addMethodCall('addBus', [$busName, new Reference($serviceId)]);

                $middlewareId = 'simple_bus.logging.profiler_middleware.'.$busName;
                $defClass = class_exists(DefinitionDecorator::class) ? DefinitionDecorator::class : ChildDefinition::class;
                $middleware = new $defClass('simple_bus.logging.profiler_middleware.abstract');
                $middleware->replaceArgument(1, $busName);
                $container->setDefinition($middlewareId, $middleware);

                $busDef = $container->getDefinition($serviceId);
                $busDef->addMethodCall('appendMiddleware', [new Reference($middlewareId)]);
            }
        }
    }

    private function getAttribute(array $tagAttributes, $attribute, $tag, $serviceId)
    {
        if (!isset($tagAttributes[$attribute])) {
            throw new \LogicException(
                sprintf(
                    'Tag "%s" of service "%s" should have an attribute "%s"',
                    $tag,
                    $serviceId,
                    $attribute
                )
            );
        }

        return $tagAttributes[$attribute];
    }
}
