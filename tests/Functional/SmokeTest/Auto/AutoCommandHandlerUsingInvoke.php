<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoCommandHandlerUsingInvoke
{
    public $handled;

    public function __invoke(AutoCommand1 $command)
    {
        $this->handled = $command;
    }
}
