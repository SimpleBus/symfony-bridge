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
    public function nestedCommandsAreExecutedSequentiallyByDefault(): void
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
    public function disablingFinishesCommandBeforeHandlingNextMiddlewareKeepsCommandNesting(): void
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
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
        static::$kernel = null;
    }
}
