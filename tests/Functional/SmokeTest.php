<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoCommand1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoCommand2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent3;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEventSubscriberUsingInvoke;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEventSubscriberUsingPublicMethod;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestCommand;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    public function it_handles_a_command_then_dispatches_events_for_all_modified_entities()
    {
        self::bootKernel(['environment' => 'config1']);
        $container = self::$kernel->getContainer();

        $this->createSchema($container);

        $commandBus = $container->get('command_bus');
        $command = new TestCommand();
        $commandBus->handle($command);

        $this->assertTrue($container->get('test_command_handler')->commandHandled);
        $this->assertTrue($container->get('test_event_subscriber')->eventHandled);

        // some_other_test_command is triggered by test_event_handler
        $this->assertTrue($container->get('some_other_test_command_handler')->commandHandled);
        $this->assertTrue($container->get('some_other_event_subscriber')->eventHandled);

        // it has logged some things
        $loggedMessages = file_get_contents($container->getParameter('log_file'));
        $this->assertStringContainsString('command_bus.DEBUG: Started handling a message', $loggedMessages);
        $this->assertStringContainsString('command_bus.DEBUG: Finished handling a message', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Started handling a message', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Finished handling a message', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Started notifying a subscriber', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Finished notifying a subscriber', $loggedMessages);
    }

    /**
     * @test
     */
    public function it_can_auto_register_event_subscribers_using_invoke()
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
    public function it_can_auto_register_event_subscribers_using_public_method()
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
    public function it_can_auto_register_command_handlers_using_invoke()
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
    public function it_can_auto_register_command_handlers_using_public_method()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $command = new AutoCommand2();

        $this->assertFalse($command->isHandled());

        $container->get('command_bus')->handle($command);

        $this->assertTrue($command->isHandled());
    }

    /**
     * @test
     * @group SymfonyBridgeProxyManagerDependency
     */
    public function fails_because_of_mising_dependency()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('In order to use bundle "DoctrineOrmBridgeBundle" you need to require "symfony/proxy-manager-bridge" package.');

        self::bootKernel(['environment' => 'config2']);
    }

    private function createSchema(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.orm.entity_manager');
        /** @var EntityManager $entityManager */

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }
}
