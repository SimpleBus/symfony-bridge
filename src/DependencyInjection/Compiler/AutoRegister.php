<?php

namespace SimpleBus\SymfonyBridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AutoRegister implements CompilerPassInterface
{
    private $tagName;
    private $tagAttribute;

    public function __construct($tagName, $tagAttribute)
    {
        $this->tagName = $tagName;
        $this->tagAttribute = $tagAttribute;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds($this->tagName) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {

                // if tag attributes are set, skip
                if (isset($tagAttributes[$this->tagAttribute])) {
                    continue;
                }

                $definition = $container->getDefinition($serviceId);

                // check if service id is class name
                $reflectionClass = new \ReflectionClass($definition->getClass() ?: $serviceId);

                // if no __invoke method, skip
                if (!$reflectionClass->hasMethod('__invoke')) {
                    continue;
                }

                $invokeParameters = $reflectionClass->getMethod('__invoke')->getParameters();

                // if no param or optional param, skip
                if (count($invokeParameters) !== 1 || $invokeParameters[0]->isOptional()) {
                    return;
                }

                // get the class name
                $handles = $invokeParameters[0]->getClass()->getName();

                // auto handle
                $definition->clearTag($this->tagName);
                $definition->addTag($this->tagName, [$this->tagAttribute => $handles]);
            }
        }
    }
}
