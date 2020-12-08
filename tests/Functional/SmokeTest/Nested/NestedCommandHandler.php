<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested;

use SimpleBus\Message\Bus\MessageBus;

final class NestedCommandHandler
{
    /**
     * @var int
     */
    private $maxNestingLevel;
    /**
     * @var RecordsBag
     */
    private $recordsBag;
    /**
     * @var MessageBus
     */
    private $commandBus;

    public function __construct(MessageBus $commandBus, RecordsBag $recordsBag, int $maxNestingLevel)
    {
        $this->commandBus = $commandBus;
        $this->recordsBag = $recordsBag;
        $this->maxNestingLevel = $maxNestingLevel;
    }

    public function handle(NestedCommand $command): void
    {
        $this->recordsBag->records[] = new PreExecutionRecord($command->level);

        if ($command->level < $this->maxNestingLevel) {
            $this->commandBus->handle(new NestedCommand($command->level + 1));
        }

        $this->recordsBag->records[] = new PostExecutionRecord($command->level);
    }
}
