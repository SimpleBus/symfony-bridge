<?php

namespace SimpleBus\SymfonyBridge\Tests\SymfonyBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AuteEvent1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AuteEvent2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AuteEvent3;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;

class ConfigureMiddlewaresTest extends TestCase
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
    public function itConfiguresAChainOfBusesAccordingToTheGivenPriorities()
    {
        $classes = [
            AuteEvent1::class => 100,
            AuteEvent2::class => -100,
            AuteEvent3::class => 200,
        ];

        foreach ($classes as $class => $priority) {
            $this->createBusDefinition($class, $priority);
        }

        $this->container->compile();

        $this->commandBusContainsMiddlewares($classes);
    }

    private function createBusDefinition($class, $priority)
    {
        $definition = new Definition($class);
        $definition->addTag($this->middlewareTag, ['priority' => $priority]);

        $this->container->setDefinition($class, $definition);

        return $definition;
    }

    private function commandBusContainsMiddlewares($expectedMiddlewareclasses)
    {
        $actualMiddlewareClasses = [];

        foreach ($this->mainBusDefinition->getMethodCalls() as $methodCall) {
            [$method, $arguments] = $methodCall;
            $this->assertSame('appendMiddleware', $method);
            $this->assertCount(1, $arguments);
            $referencedService = $arguments[0];

            if (Kernel::VERSION_ID >= 40000) {
                $this->assertInstanceOf(
                    'Symfony\Component\DependencyInjection\Definition',
                    $referencedService
                );
            } else {
                $this->assertInstanceOf(
                    'Symfony\Component\DependencyInjection\Reference',
                    $referencedService
                );
                $referencedService = $this->container->getDefinition((string) $referencedService);
            }

            $actualMiddlewareClasses[$referencedService->getClass()] = $referencedService->getTag('middleware')[0]['priority'];
        }

        $this->assertEquals($expectedMiddlewareclasses, $actualMiddlewareClasses);
    }
}
