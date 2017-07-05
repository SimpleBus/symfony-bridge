<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEventSubscriber
{
    public $handled;

    public function __invoke(AutoEvent $event)
    {
        $this->handled = $event;
    }
}
