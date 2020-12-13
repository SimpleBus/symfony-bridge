<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait CollectServices
{
    protected function collectServiceIds(
        ContainerBuilder $container,
        $tagName,
        $keyAttribute,
        callable $callback
    ) {
        foreach ($container->findTaggedServiceIds($tagName) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                if (!isset($tagAttributes[$keyAttribute])) {
                    throw new \InvalidArgumentException(sprintf('The attribute "%s" of tag "%s" of service "%s" is mandatory', $keyAttribute, $tagName, $serviceId));
                }

                $key = $tagAttributes[$keyAttribute];

                call_user_func($callback, $key, $serviceId, $tagAttributes);
            }
        }
    }
}
