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
                // if tag attribute is set, skip
                if (isset($tagAttributes[$this->tagAttribute])) {
                    continue;
                }

                $registerPublicMethods = false;
                if (isset($tagAttributes['register_public_methods']) && true === $tagAttributes['register_public_methods']) {
                    $registerPublicMethods = true;
                }

                $definition = $container->getDefinition($serviceId);

                // check if service id is class name
                $reflectionClass = new \ReflectionClass($definition->getClass() ?: $serviceId);

                $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

                $tagAttributes = [];
                foreach ($methods as $method) {
                    if (true === $method->isConstructor()) {
                        continue;
                    }

                    if (true === $method->isDestructor()) {
                        continue;
                    }

                    if (false === $registerPublicMethods && '__invoke' !== $method->getName()) {
                        continue;
                    }

                    $parameters = $method->getParameters();

                    // if no param or optional param, skip
                    if (count($parameters) !== 1 || $parameters[0]->isOptional()) {
                        continue;
                    }

                    // get the class name
                    $handles = $parameters[0]->getClass()->getName();

                    $tagAttributes[] = [
                        $this->tagAttribute => $handles,
                        'method' => $method->getName()
                    ];
                }

                if (count($tags) !== 0) {
                    // auto handle
                    $definition->clearTag($this->tagName);

                    foreach ($tagAttributes as $attributes) {
                        $definition->addTag($this->tagName, $attributes);
                    }
                }
            }
        }
    }
}
