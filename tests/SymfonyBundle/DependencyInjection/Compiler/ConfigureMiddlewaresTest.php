<?php

namespace SimpleBus\SymfonyBridge\Tests\SymfonyBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent3;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ConfigureMiddlewaresTest extends TestCase
{
    private ContainerBuilder $container;

    private string $mainBusId = 'main_bus';

    private string $middlewareTag = 'middleware';

    private Definition $mainBusDefinition;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->mainBusDefinition = new Definition('stdClass');
        $this->mainBusDefinition->setPublic(true);
        $this->container->setDefinition($this->mainBusId, $this->mainBusDefinition);
        $this->container->addCompilerPass(new ConfigureMiddlewares($this->mainBusId, $this->middlewareTag));
    }

    /**
     * @test
     */
    public function itConfiguresAChainOfBusesAccordingToTheGivenPriorities(): void
    {
        $classes = [
            AutoEvent1::class => 100,
            AutoEvent2::class => -100,
            AutoEvent3::class => 200,
        ];

        foreach ($classes as $class => $priority) {
            $this->createBusDefinition($class, $priority);
        }

        $this->container->compile();

        $this->commandBusContainsMiddlewares($classes);
    }

    /**
     * @param class-string $class
     */
    private function createBusDefinition(string $class, int $priority): Definition
    {
        $definition = new Definition($class);
        $definition->addTag($this->middlewareTag, ['priority' => $priority]);

        $this->container->setDefinition($class, $definition);

        return $definition;
    }

    /**
     * @param array<class-string, int> $expectedMiddlewareclasses
     */
    private function commandBusContainsMiddlewares(array $expectedMiddlewareclasses): void
    {
        $actualMiddlewareClasses = [];

        foreach ($this->mainBusDefinition->getMethodCalls() as $methodCall) {
            [$method, $arguments] = $methodCall;
            $this->assertSame('appendMiddleware', $method);
            $this->assertCount(1, $arguments);
            $referencedService = $arguments[0];

            $this->assertInstanceOf(
                'Symfony\Component\DependencyInjection\Definition',
                $referencedService
            );

            $actualMiddlewareClasses[$referencedService->getClass()] = $referencedService->getTag('middleware')[0]['priority'];
        }

        $this->assertEquals($expectedMiddlewareclasses, $actualMiddlewareClasses);
    }
}
