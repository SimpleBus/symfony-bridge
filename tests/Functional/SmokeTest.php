<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

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
    protected static function getKernelClass()
    {
        return TestKernel::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
        static::$kernel = null;
    }

    /**
     * @test
     */
    public function itCanAutoRegisterEventSubscribersUsingInvoke()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $event = new AutoEvent1();

        $this->assertFalse($event->isHandledBy(AutoEventSubscriberUsingInvoke::class));

        $container->get('event_bus')->handle($event);

        $this->assertTrue($event->isHandledBy(AutoEventSubscriberUsingInvoke::class));
    }

    /**
     * @test
     */
    public function itCanAutoRegisterEventSubscribersUsingPublicMethod()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $event2 = new AutoEvent2();
        $event3 = new AutoEvent3();

        $this->assertFalse($event2->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));
        $this->assertFalse($event3->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));

        $container->get('event_bus')->handle($event2);
        $container->get('event_bus')->handle($event3);

        $this->assertTrue($event2->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));
        $this->assertTrue($event3->isHandledBy(AutoEventSubscriberUsingPublicMethod::class));
    }

    /**
     * @test
     */
    public function itCanAutoRegisterCommandHandlersUsingInvoke()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $command = new AutoCommand1();

        $this->assertFalse($command->isHandled());

        $container->get('command_bus')->handle($command);

        $this->assertTrue($command->isHandled());
    }

    /**
     * @test
     */
    public function itCanAutoRegisterCommandHandlersUsingPublicMethod()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $command = new AutoCommand2();

        $this->assertFalse($command->isHandled());

        $container->get('command_bus')->handle($command);

        $this->assertTrue($command->isHandled());
    }
}
