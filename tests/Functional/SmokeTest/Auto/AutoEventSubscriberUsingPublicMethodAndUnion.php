<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEventSubscriberUsingPublicMethodAndUnion
{
    public function __invoke(AutoEvent2|AutoEvent3 $event): void
    {
        $event->setHandledBy($this);
    }
}
