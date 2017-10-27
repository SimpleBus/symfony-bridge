<?php

namespace SimpleBus\SymfonyBridge\Tests\SymfonyBundle\DependencyInjection\Compiler;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;

class ConfigureMiddlewaresTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    private $mainBusId = 'main_bus';

    private $middlewareTag = 'middleware';

    /**
     * @var Definition
     */
    private $mainBusDefinition;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->mainBusDefinition = new Definition('stdClass');
        $this->container->setDefinition($this->mainBusId, $this->mainBusDefinition);
        $this->container->addCompilerPass(new ConfigureMiddlewares($this->mainBusId, $this->middlewareTag));
    }

    /**
     * @test
     */
    public function it_configures_a_chain_of_buses_according_to_the_given_priorities()
    {
        $this->createBusDefinition('middleware100', 100);
        $this->createBusDefinition('middleware-100', -100);
        $this->createBusDefinition('middleware200', 200);

        $this->container->compile();

        if (Kernel::VERSION_ID >= 40000) {
            $this->commandBusContainsMiddlewaresInSymfony4(array(200, 100, -100));
        } else {
            $this->commandBusContainsMiddlewares(array('middleware200', 'middleware100', 'middleware-100'));
        }
    }

    private function createBusDefinition($id, $priority)
    {
        $definition = new Definition('stdClass');
        $definition->addTag($this->middlewareTag, array('priority' => $priority));

        $this->container->setDefinition($id, $definition);

        return $definition;
    }

    private function commandBusContainsMiddlewares($expectedMiddlewareIds)
    {
        $actualMiddlewareIds = [];

        foreach ($this->mainBusDefinition->getMethodCalls() as $methodCall) {
            list($method, $arguments) = $methodCall;
            $this->assertSame('appendMiddleware', $method);
            $this->assertCount(1, $arguments);
            $referencedService = $arguments[0];
            $this->assertInstanceOf(
                'Symfony\Component\DependencyInjection\Reference',
                $referencedService
            );
            $actualMiddlewareIds[] = (string) $referencedService;
        }

        $this->assertEquals($expectedMiddlewareIds, $actualMiddlewareIds);
    }

    private function commandBusContainsMiddlewaresInSymfony4($expectedMiddlewareIds)
    {
        $middlewaresPriorities = [];

        foreach ($this->mainBusDefinition->getMethodCalls() as $methodCall) {
            list($method, $arguments) = $methodCall;
            $this->assertSame('appendMiddleware', $method);
            $this->assertCount(1, $arguments);
            /** @var Definition $serviceDefinition */
            $serviceDefinition = $arguments[0];
            $this->assertInstanceOf(
                Definition::class,
                $serviceDefinition
            );

            $middlewaresPriorities[] = (string) $serviceDefinition->getTag('middleware')[0]['priority'];
        }

        $this->assertEquals($expectedMiddlewareIds, $middlewaresPriorities);
    }
}
