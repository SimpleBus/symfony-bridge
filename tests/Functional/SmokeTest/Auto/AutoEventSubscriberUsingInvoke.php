<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEventSubscriberUsingInvoke
{
    public function __invoke(AutoEvent1 $event): void
    {
        $event->setHandledBy($this);
    }

    public function randomPublicMethod(string $value): void
    {
    }
}
