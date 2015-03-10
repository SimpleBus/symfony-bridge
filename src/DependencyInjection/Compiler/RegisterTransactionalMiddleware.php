<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterTransactionalMiddleware implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $transactionalMiddlewareServiceId = 'simple_bus.doctrine_orm_bridge.wraps_next_command_in_transaction';
        if (!($container->has($transactionalMiddlewareServiceId))) {
            return;
        }

        $transactionalMiddlewareService = $container->findDefinition($transactionalMiddlewareServiceId);
        $registerTransactionalMiddlewareForTypes = ['command'];
        $messageBusTag = 'message_bus';
        $middlewarePriority = 100;

        foreach ($container->findTaggedServiceIds($messageBusTag) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                $type = $this->getAttribute($tagAttributes, 'type', $messageBusTag, $serviceId);
                if (!in_array($type, $registerTransactionalMiddlewareForTypes)) {
                    continue;
                }

                /*
                 * This is equivalent to:
                 *
                 *     services:
                 *         %transactional_middleware_service_id%:
                 *         ...
                 *         tags:
                 *             - { name: %middleware_tag%, priority: %priority% }
                 */
                $middlewareTag = $this->getAttribute($tagAttributes, 'middleware_tag', $messageBusTag, $serviceId);
                $transactionalMiddlewareService->addTag($middlewareTag, ['priority' => $middlewarePriority]);
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
