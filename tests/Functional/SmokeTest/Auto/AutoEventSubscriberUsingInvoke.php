<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEventSubscriberUsingInvoke
{
    public function __invoke(AutoEvent1 $event)
    {
        $event->setHandledBy($this);
    }

    public function randomPublicMethod($value)
    {

    }
}
