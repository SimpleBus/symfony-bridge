<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

class SomeOtherEventSubscriber
{
    public $eventHandled = false;

    public function notify()
    {
        $this->eventHandled = true;
    }
}
