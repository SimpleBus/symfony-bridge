<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Message\Message;
use SimpleBus\Message\Subscriber\MessageSubscriber;

class TestEntityCreatedEventSubscriber implements MessageSubscriber
{
    private $commandBus;
    public $eventHandled = false;

    public function __construct(MessageBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function notify(Message $message)
    {
        $this->eventHandled = true;

        $this->commandBus->handle(new SomeOtherTestCommand());
    }
}
