<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoCommandHandlerUsingPublicMethod
{
    public $handled;

    public function someHandleMethod(AutoCommand2 $command)
    {
        $this->handled = $command;
    }
}
