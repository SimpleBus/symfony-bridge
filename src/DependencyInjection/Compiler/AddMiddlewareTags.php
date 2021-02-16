<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddMiddlewareTags implements CompilerPassInterface
{
    private const MESSAGE_BUS_TAG = 'message_bus';

    private string $middlewareServiceId;

    /**
     * @var string[]
     */
    private array $addTagForMessageBusesOfTypes;

    private int $middlewarePriority;

    /**
     * @param string[] $addTagForMessageBusesOfTypes
     */
    public function __construct(string $middlewareServiceId, array $addTagForMessageBusesOfTypes, int $middlewarePriority)
    {
        $this->middlewareServiceId = $middlewareServiceId;
        $this->addTagForMessageBusesOfTypes = $addTagForMessageBusesOfTypes;
        $this->middlewarePriority = $middlewarePriority;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!($container->has($this->middlewareServiceId))) {
            return;
        }

        $transactionalMiddlewareService = $container->findDefinition($this->middlewareServiceId);

        foreach ($container->findTaggedServiceIds(self::MESSAGE_BUS_TAG) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                $type = $this->getAttribute($tagAttributes, 'type', self::MESSAGE_BUS_TAG, $serviceId);
                if (!in_array($type, $this->addTagForMessageBusesOfTypes)) {
                    continue;
                }

                $middlewareTag = $this->getAttribute(
                    $tagAttributes,
                    'middleware_tag',
                    self::MESSAGE_BUS_TAG,
                    $serviceId
                );

                /*
                 * This is equivalent to:
                 *
                 *     services:
                 *         %transactional_middleware_service_id%:
                 *         ...
                 *         tags:
                 *             - { name: %middleware_tag%, priority: %priority% }
                 */
                $transactionalMiddlewareService->addTag($middlewareTag, ['priority' => $this->middlewarePriority]);
            }
        }
    }

    /**
     * @param array<string, string> $tagAttributes
     */
    private function getAttribute(array $tagAttributes, string $attribute, string $tag, string $serviceId): string
    {
        if (!isset($tagAttributes[$attribute])) {
            throw new LogicException(sprintf('Tag "%s" of service "%s" should have an attribute "%s"', $tag, $serviceId, $attribute));
        }

        return $tagAttributes[$attribute];
    }
}
