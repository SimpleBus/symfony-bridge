<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoCommandHandlerUsingPublicMethod
{
    public function someHandleMethod(AutoCommand2 $command)
    {
        $command->setHandled(true);
    }

    public function randomPublicMethod($value)
    {
    }
}
