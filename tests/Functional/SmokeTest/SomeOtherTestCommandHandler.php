<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;

class SomeOtherTestCommandHandler implements MessageHandler
{
    public $commandHandled = false;
    private $eventBus;

    public function __construct(MessageBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function handle(Message $command)
    {
        $this->commandHandled = true;

        // it's possible to directly call the event bus
        $this->eventBus->handle(new SomeOtherEvent());
    }
}
