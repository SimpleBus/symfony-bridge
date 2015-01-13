<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Message;
use SimpleBus\Message\Subscriber\MessageSubscriber;

class SomeOtherEventSubscriber implements MessageSubscriber
{
    public $eventHandled = false;

    public function notify(Message $event)
    {
        $this->eventHandled = true;
    }
}
