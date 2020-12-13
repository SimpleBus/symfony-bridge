<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEventSubscriberUsingPublicMethod
{
    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public function someEventHandler(AutoEvent2 $event)
    {
        $event->setHandledBy($this);
    }

    public function someOtherEventHandler(AutoEvent3 $event)
    {
        $event->setHandledBy($this);
    }

    public function randomPublicMethod($value)
    {
    }
}
