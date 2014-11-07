<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Command\Bus\CommandBus;
use SimpleBus\Event\Event;
use SimpleBus\Event\Handler\EventHandler;

class TestEntityCreatedEventHandler implements EventHandler
{
    private $commandBus;
    public $eventHandled = false;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handle(Event $event)
    {
        $this->eventHandled = true;

        $this->commandBus->handle(new SomeOtherTestCommand());
    }
}
