<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEvent3
{
    private $handled = [];

    public function isHandledBy($subscriber): bool
    {
        return in_array($subscriber, $this->handled);
    }

    public function setHandledBy($subscriber): void
    {
        $this->handled[] = get_class($subscriber);
    }
}
