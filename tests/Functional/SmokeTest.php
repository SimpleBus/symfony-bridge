<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoCommand1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoCommand2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent3;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEventSubscriberUsingInvoke;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEventSubscriberUsingPublicMethod;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SmokeTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
    }

    /**
     * @test
     */
    public function itCanAutoRegisterEventSubscribersUsingInvoke(): void
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $event = new AutoEvent1();

        $this->assertFalse($event->isHandledBy(AutoEventSubscriberUsingInvoke::class));

        $eventBus = $container->get('event_bus');

        $this->assertInstanceOf(MessageBus::class, $eventBus);

        $eventBus->handle($event);

        $this->assertTrue($event->isHandledBy(AutoEventSubscriberUsingInvoke::class));
    }

    /**
     * @test
     */
    public function itCanAutoRegisterEventSubscribersUsingPublicMethod(): void
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $event2 = new AutoEvent2();
        $event3 = new AutoEvent3();

        $this->assertFalse($event2->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));
        $this->assertFalse($event3->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));

        $eventBus = $container->get('event_bus');

        $this->assertInstanceOf(MessageBus::class, $eventBus);

        $eventBus->handle($event2);
        $eventBus->handle($event3);

        $this->assertTrue($event2->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));
        $this->assertTrue($event3->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));
    }

    /**
     * @test
     */
    public function itCanAutoRegisterCommandHandlersUsingInvoke(): void
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $command = new AutoCommand1();

        $this->assertFalse($command->isHandled());

        $commandBus = $container->get('command_bus');

        $this->assertInstanceOf(MessageBus::class, $commandBus);

        $commandBus->handle($command);

        $this->assertTrue($command->isHandled());
    }

    /**
     * @test
     */
    public function itCanAutoRegisterCommandHandlersUsingPublicMethod(): void
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $command = new AutoCommand2();

        $this->assertFalse($command->isHandled());

        $commandBus = $container->get('command_bus');

        $this->assertInstanceOf(MessageBus::class, $commandBus);

        $commandBus->handle($command);

        $this->assertTrue($command->isHandled());
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
