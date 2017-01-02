<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use SimpleBus\Message\Name\NamedMessage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *
 */
final class RegisterAutowiredSubscribers implements CompilerPassInterface
{
    private $serviceId;
    private $autowiredSubscriberTag;
    private $manualSubscriberTag;

    /**
     * @param string  $serviceId              The service id of the MessageSubscriberCollection
     * @param string  $autowiredSubscriberTag The tag name of autowired message subscriber services
     * @param string  $manualSubscriberTag    The tag name of manual message subscriber services
     */
    public function __construct($serviceId, $autowiredSubscriberTag, $manualSubscriberTag)
    {
        $this->serviceId = $serviceId;
        $this->autowiredSubscriberTag = $autowiredSubscriberTag;
        $this->manualSubscriberTag = $manualSubscriberTag;
    }

    /**
     * Search for message autowired subscriber services and provide them as a constructor argument to the message subscriber
     * collection service.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->serviceId)) {
            return;
        }

        $definition = $container->findDefinition($this->serviceId);

        $handlers = $definition->getArgument(0);

        foreach ($container->findTaggedServiceIds($this->autowiredSubscriberTag) as $serviceId => $tags) {
            if (count($tags) > 1) {
                throw new RuntimeException(
                    sprintf('Service "%s" must contain the only "%s" tag.', $serviceId, $this->autowiredSubscriberTag)
                );
            }

            $tag = $tags[0];

            $subscriberDefinition = $container->getDefinition($serviceId);

            if ($subscriberDefinition->hasTag($this->manualSubscriberTag)) {
                throw new RuntimeException(
                    sprintf('Service "%s" must contain either "%s" or "%s" tag, not both of them.', $serviceId, $tag, $this->manualSubscriberTag)
                );
            }

            if (!$subscriberDefinition->getClass()) {
                throw new RuntimeException(
                    sprintf('Class is missing in the "%s" service.', $serviceId)
                );
            }

            $handlers = array_merge_recursive(
                $handlers,
                $this->getHandlersFrom(new ReflectionClass($subscriberDefinition->getClass()), $serviceId)
            );
        }

        $definition->replaceArgument(0, $handlers);
    }

    private function getHandlersFrom(ReflectionClass $class, $serviceId)
    {
        $handlers = [];

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                $method->getNumberOfParameters() !== 1
                || !$method->getParameters()[0]->getClass()
            ) {
                throw new RuntimeException(
                    sprintf(
                        'Method "%s::%s" in service "%s" contains inappropriate public method for autowiring.'
                        . ' You can try to register your subscribers manually with the "%s" tag.',
                        $class->getName(),
                        $method->getName(),
                        $serviceId,
                        $this->manualSubscriberTag
                    )
                );
            }

            $eventClass = $method->getParameters()[0]->getClass();

            if ($eventClass->implementsInterface(NamedMessage::class)) {
                throw new RuntimeException(
                    sprintf(
                        'Event "%s" is incompatible with autowiring feature because it implements the %s in service "%s".'
                        . ' You can try to register your subscribers manually with the "%s" tag.',
                        $eventClass->getName(),
                        NamedMessage::class,
                        $serviceId,
                        $this->manualSubscriberTag
                    )
                );
            }

            $handlers[$eventClass->getName()][] = [$serviceId, $method->getName()];
        }

        return $handlers;
    }
}
