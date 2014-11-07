<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterHandlers implements CompilerPassInterface
{
    private $serviceId;
    private $tag;
    private $keyAttribute;
    private $acceptMultiple;

    public function __construct($serviceId, $tag, $keyAttribute, $acceptMultiple)
    {
        $this->serviceId = $serviceId;
        $this->tag = $tag;
        $this->keyAttribute = $keyAttribute;
        $this->acceptMultiple = $acceptMultiple;
    }

    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition($this->serviceId);

        $handlers = array();

        foreach ($container->findTaggedServiceIds($this->tag) as $handlerId => $tags) {
            foreach ($tags as $tagAttributes) {
                if (!isset($tagAttributes[$this->keyAttribute])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Tag "%s" of service "%s" should have a "%s" attribute',
                            $this->tag,
                            $handlerId,
                            $this->keyAttribute
                        )
                    );
                }

                $key = $tagAttributes[$this->keyAttribute];
                if ($this->acceptMultiple) {
                    $handlers[$key][] = $handlerId;
                } else {
                    $handlers[$key] = $handlerId;
                }
            }
        }

        $definition->replaceArgument(1, $handlers);
    }
}
