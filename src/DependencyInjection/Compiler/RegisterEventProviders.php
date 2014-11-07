<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterEventProviders implements CompilerPassInterface
{
    private $aggregatorId;
    private $collectorTag;

    public function __construct($aggregatorId, $collectorTag)
    {
        $this->aggregatorId = $aggregatorId;
        $this->collectorTag = $collectorTag;
    }

    public function process(ContainerBuilder $container)
    {
        $aggregator = $container->findDefinition($this->aggregatorId);

        $collectors = array();
        foreach (array_keys($container->findTaggedServiceIds($this->collectorTag)) as $collectorId) {
            $collectors[] = new Reference($collectorId);
        }

        $aggregator->replaceArgument(0, $collectors);
    }
}
