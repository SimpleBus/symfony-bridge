<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent3;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEventSubscriberUsingPublicMethodAndUnion;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 * @coversNothing
 * @requires PHP 8.0
 */
class Php8SmokeTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
    }

    /**
     * @test
     */
    public function itCanAutoRegisterEventSubscribersUsingPublicMethodAndUnion(): void
    {
        self::bootKernel(['environment' => 'config2_php8']);
        $container = self::$kernel->getContainer();

        $event2 = new AutoEvent2();
        $event3 = new AutoEvent3();

        $this->assertFalse($event2->isHandledBy(AutoEventSubscriberUsingPublicMethodAndUnion::class));
        $this->assertFalse($event3->isHandledBy(AutoEventSubscriberUsingPublicMethodAndUnion::class));

        $eventBus = $container->get('event_bus');

        $this->assertInstanceOf(MessageBus::class, $eventBus);

        $eventBus->handle($event2);
        $eventBus->handle($event3);

        $this->assertTrue($event2->isHandledBy(AutoEventSubscriberUsingPublicMethodAndUnion::class));
        $this->assertTrue($event3->isHandledBy(AutoEventSubscriberUsingPublicMethodAndUnion::class));
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
