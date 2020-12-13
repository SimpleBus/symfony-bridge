<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoCommand2
{
    private $handled = false;

    public function isHandled(): bool
    {
        return $this->handled;
    }

    public function setHandled(bool $handled): void
    {
        $this->handled = $handled;
    }
}
