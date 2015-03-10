<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterLoggingMiddleware implements CompilerPassInterface
{
    /**
     * @var
     */
    private $loggingMiddlewareServiceId;
    /**
     * @var
     */
    private $enabledParameterName;
    /**
     * @var
     */
    private $channelParameterName;
    /**
     * @var
     */
    private $middlewareTag;
    /**
     * @var int
     */
    private $priority;

    public function __construct(
        $loggingMiddlewareServiceId,
        $enabledParameterName,
        $channelParameterName,
        $middlewareTag,
        $priority = -999
    ) {
        $this->loggingMiddlewareServiceId = $loggingMiddlewareServiceId;
        $this->enabledParameterName = $enabledParameterName;
        $this->channelParameterName = $channelParameterName;
        $this->middlewareTag = $middlewareTag;
        $this->priority = $priority;
    }

    public function process(ContainerBuilder $container)
    {
        if (
            !$container->has($this->loggingMiddlewareServiceId) ||
            !$container->hasParameter($this->enabledParameterName) ||
            !$container->getParameter($this->enabledParameterName)
        ) {
            return;
        }

        $definition = $container->findDefinition($this->loggingMiddlewareServiceId);

        $definition->addTag(
            $this->middlewareTag,
            ['priority' => $this->priority]
        );

        $channel = $container->getParameter($this->channelParameterName);
        $definition->addTag(
            'monolog.logger',
            ['channel' => $channel]
        );
    }
}
