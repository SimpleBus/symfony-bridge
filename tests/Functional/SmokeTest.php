<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoCommand1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoCommand2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent1;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent2;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto\AutoEvent3;
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
        $this->assertContains('command_bus.DEBUG: Started handling a message', $loggedMessages);
        $this->assertContains('command_bus.DEBUG: Finished handling a message', $loggedMessages);
        $this->assertContains('event_bus.DEBUG: Started handling a message', $loggedMessages);
        $this->assertContains('event_bus.DEBUG: Finished handling a message', $loggedMessages);
        $this->assertContains('event_bus.DEBUG: Started notifying a subscriber', $loggedMessages);
        $this->assertContains('event_bus.DEBUG: Finished notifying a subscriber', $loggedMessages);
    }

    /**
     * @test
     */
    public function it_can_auto_register_event_subscribers_using_invoke()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $subscriber = $container->get('auto_event_subscriber_using_invoke');
        $event = new AutoEvent1();

        $this->assertNull($subscriber->handled);

        $container->get('event_bus')->handle($event);

        $this->assertSame($event, $subscriber->handled);
    }

    /**
     * @test
     */
    public function it_can_auto_register_event_subscribers_using_public_method()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $subscriber = $container->get('auto_event_subscriber_using_public_method');
        $event2 = new AutoEvent2();
        $event3 = new AutoEvent3();

        $this->assertEmpty($subscriber->handled);

        $container->get('event_bus')->handle($event2);
        $container->get('event_bus')->handle($event3);

        $this->assertSame([$event2, $event3], $subscriber->handled);
    }

    /**
     * @test
     */
    public function it_can_auto_register_command_handlers_using_invoke()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $handler = $container->get('auto_command_handler_using_invoke');
        $command = new AutoCommand1();

        $this->assertNull($handler->handled);

        $container->get('command_bus')->handle($command);

        $this->assertSame($command, $handler->handled);
    }

    /**
     * @test
     */
    public function it_can_auto_register_command_handlers_using_public_method()
    {
        self::bootKernel(['environment' => 'config2']);
        $container = self::$kernel->getContainer();

        $handler = $container->get('auto_command_handler_using_public_method');
        $command = new AutoCommand2();

        $this->assertNull($handler->handled);

        $container->get('command_bus')->handle($command);

        $this->assertSame($command, $handler->handled);
    }

    /**
     * @test
     * 
     * @group depTest
     *
     * @expectedException        \LogicException
     * @expectedExceptionMessage In order to use bundle "DoctrineOrmBridgeBundle" you need to require "symfony/proxy-manager-bridge" package.
     */
    public function fails_because_of_mising_dependency()
    {
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
