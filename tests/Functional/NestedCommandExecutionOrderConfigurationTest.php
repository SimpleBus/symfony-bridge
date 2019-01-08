<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\NestedCommand;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\PostExecutionRecord;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\PreExecutionRecord;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\RecordsBag;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class NestedCommandExecutionOrderConfigurationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @test
     */
    public function nested_commands_are_executed_sequentially_by_default(): void
    {
        self::bootKernel(['environment' => 'config1']);
        $container = self::$kernel->getContainer();

        $commandBus = $container->get('command_bus');
        $command = new NestedCommand();
        $commandBus->handle($command);

        /** @var RecordsBag $recorder */
        $recorder = $container->get('nesting_records_bag');

        $this->assertEquals(
            [
                new PreExecutionRecord(0),
                new PostExecutionRecord(0),
                new PreExecutionRecord(1),
                new PostExecutionRecord(1),
                new PreExecutionRecord(2),
                new PostExecutionRecord(2),

            ],
            $recorder->records
        );
    }

    /**
     * @test
     */
    public function disabling_finishes_command_before_handling_next_middleware_keeps_command_nesting(): void
    {
        self::bootKernel(['environment' => 'config3']);
        $container = self::$kernel->getContainer();

        $commandBus = $container->get('command_bus');
        $command = new NestedCommand();
        $commandBus->handle($command);

        /** @var RecordsBag $recorder */
        $recorder = $container->get('nesting_records_bag');

        $this->assertEquals(
            [
                new PreExecutionRecord(0),
                new PreExecutionRecord(1),
                new PreExecutionRecord(2),
                new PostExecutionRecord(2),
                new PostExecutionRecord(1),
                new PostExecutionRecord(0),

            ],
            $recorder->records
        );
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        parent::tearDown();

        static::$class = null;
        static::$kernel = null;
    }
}
