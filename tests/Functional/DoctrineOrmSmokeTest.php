<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\DoctrineTestKernel;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class DoctrineOrmSmokeTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
        static::$kernel = null;
    }

    /**
     * @test
     */
    public function itHandlesACommandThenDispatchesEventsForAllModifiedEntities()
    {
        if (!class_exists('Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator')) {
            $this->markTestSkipped('This test requires "symfony/proxy-manager-bridge" to be installed.');

            return;
        }

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
    public function failsBecauseOfMisingDependency()
    {
        if (class_exists('Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator')) {
            $this->markTestSkipped('This test requires "symfony/proxy-manager-bridge" to NOT be installed.');

            return;
        }

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('In order to use bundle "DoctrineOrmBridgeBundle" you need to require "symfony/proxy-manager-bridge" package.');

        self::bootKernel(['environment' => 'config2']);
    }

    protected static function getKernelClass()
    {
        return DoctrineTestKernel::class;
    }

    private function createSchema(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.orm.entity_manager');
        /** @var EntityManager $entityManager */
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }
}
