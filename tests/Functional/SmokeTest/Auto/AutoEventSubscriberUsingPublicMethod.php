<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEventSubscriberUsingPublicMethod
{
    public $handled = [];

    public function __construct()
    {

    }

    public function __destruct()
    {

    }

    public function someEventHandler(AutoEvent2 $event)
    {
        $this->handled[] = $event;
    }

    public function someOtherEventHandler(AutoEvent3 $event)
    {
        $this->handled[] = $event;
    }
}
