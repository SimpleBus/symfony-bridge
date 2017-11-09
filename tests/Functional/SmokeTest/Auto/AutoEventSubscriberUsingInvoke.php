<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEventSubscriberUsingInvoke
{
    public $handled;

    public function __invoke(AutoEvent1 $event)
    {
        $this->handled = $event;
    }

    public function randomPublicMethod($value)
    {

    }
}
