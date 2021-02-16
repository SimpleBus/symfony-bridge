<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Bus\MessageBus;

class TestEntityCreatedEventSubscriber
{
    public bool $eventHandled = false;
    private MessageBus $commandBus;

    public function __construct(MessageBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function notify(): void
    {
        $this->eventHandled = true;

        $this->commandBus->handle(new SomeOtherTestCommand());
    }
}
