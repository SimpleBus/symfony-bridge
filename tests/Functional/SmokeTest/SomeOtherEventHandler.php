<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Event\Event;
use SimpleBus\Event\Handler\EventHandler;

class SomeOtherEventHandler implements EventHandler
{
    public $eventHandled = false;

    public function handle(Event $event)
    {
        $this->eventHandled = true;
    }
}
