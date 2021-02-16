<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterHandlers implements CompilerPassInterface
{
    use CollectServices;

    private string $callableServiceId;
    private string $serviceLocatorId;
    private string $tag;
    private string $keyAttribute;

    /**
     * @param string $callableServiceId The service id of the MessageHandlerMap
     * @param string $serviceLocatorId  The service id of the ServiceLocator
     * @param string $tag               The tag name of message handler services
     * @param string $keyAttribute      The name of the tag attribute that contains the name of the handler
     */
    public function __construct(string $callableServiceId, string $serviceLocatorId, string $tag, string $keyAttribute)
    {
        $this->callableServiceId = $callableServiceId;
        $this->serviceLocatorId = $serviceLocatorId;
        $this->tag = $tag;
        $this->keyAttribute = $keyAttribute;
    }

    /**
     * Search for message handler services and provide them as a constructor argument to the message handler map
     * service.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->callableServiceId)) {
            return;
        }

        if (!$container->has($this->serviceLocatorId)) {
            return;
        }

        $callableDefinition = $container->findDefinition($this->callableServiceId);
        $serviceLocatorDefinition = $container->findDefinition($this->serviceLocatorId);

        $handlers = [];
        $services = [];

        $this->collectServiceIds(
            $container,
            $this->tag,
            $this->keyAttribute,
            function ($key, $serviceId, array $tagAttributes) use (&$handlers, &$services) {
                if (isset($tagAttributes['method'])) {
                    // Symfony 3.3 supports services by classname. This interferes with `is_callable`
                    // in `ServiceLocatorAwareCallableResolver`
                    $callable = [
                        'serviceId' => $serviceId,
                        'method' => $tagAttributes['method'],
                    ];
                } else {
                    $callable = $serviceId;
                }

                $handlers[ltrim($key, '\\')] = $callable;
                $services[$serviceId] = new Reference($serviceId);
            }
        );

        $callableDefinition->replaceArgument(0, $handlers);
        $serviceLocatorDefinition->replaceArgument(0, $services);
    }
}
