<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterMessageRecorders implements CompilerPassInterface
{
    private $aggregatorId;
    private $recorderTag;

    /**
     * @param string  $aggregatorId         The id of the service with class AggregatesRecordedMessages
     * @param string  $recorderTag          The tag name of message recorder services
     */
    public function __construct($aggregatorId, $recorderTag)
    {
        $this->aggregatorId = $aggregatorId;
        $this->recorderTag = $recorderTag;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->aggregatorId)) {
            return;
        }

        $aggregator = $container->findDefinition($this->aggregatorId);

        $recorders = [];
        foreach (array_keys($container->findTaggedServiceIds($this->recorderTag)) as $recorderId) {
            $recorders[] = new Reference($recorderId);
        }

        $aggregator->replaceArgument(0, $recorders);
    }
}
