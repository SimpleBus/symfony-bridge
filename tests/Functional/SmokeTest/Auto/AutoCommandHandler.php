<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoCommandHandler
{
    public $handled;

    public function __invoke(AutoCommand $command)
    {
        $this->handled = $command;
    }
}
