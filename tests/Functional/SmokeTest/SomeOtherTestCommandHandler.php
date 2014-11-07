<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Command\Command;
use SimpleBus\Command\Handler\CommandHandler;
use SimpleBus\Event\Bus\EventBus;

class SomeOtherTestCommandHandler implements CommandHandler
{
    public $commandHandled = false;
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function handle(Command $command)
    {
        $this->commandHandled = true;

        // it's possible to directly call the event bus
        $this->eventBus->handle(new SomeOtherEvent());
    }
}
