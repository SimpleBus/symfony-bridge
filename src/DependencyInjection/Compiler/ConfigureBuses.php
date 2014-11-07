<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigureBuses implements CompilerPassInterface
{
    private $mainBusId;
    private $busTag;

    public function __construct($mainBusId, $busTag)
    {
        $this->mainBusId = $mainBusId;
        $this->busTag = $busTag;
    }

    public function process(ContainerBuilder $container)
    {
        $busIds = new \SplPriorityQueue();

        foreach ($container->findTaggedServiceIds($this->busTag) as $specializedBusId => $tags) {
            foreach ($tags as $tagAttributes) {
                $priority = isset($tagAttributes['priority']) ? $tagAttributes['priority'] : 0;
                $busIds->insert($specializedBusId, $priority);
            }
        }

        $orderedBusIds = iterator_to_array($busIds, false);
        $numberOfBuses = count($orderedBusIds);

        if ($numberOfBuses === 0) {
            return;
        }

        for ($i = 0; $i < $numberOfBuses - 1; $i++) {
            $busId = $orderedBusIds[$i];
            $nextBusId = $orderedBusIds[$i + 1];
            $definition = $container->findDefinition($busId);
            $definition->addMethodCall('setNext', array(new Reference($nextBusId)));
        }

        $container->setAlias($this->mainBusId, reset($orderedBusIds));
    }
}
