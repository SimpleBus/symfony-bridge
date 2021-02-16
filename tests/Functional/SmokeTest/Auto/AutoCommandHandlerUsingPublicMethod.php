<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoCommandHandlerUsingPublicMethod
{
    public function someHandleMethod(AutoCommand2 $command): void
    {
        $command->setHandled(true);
    }

    public function randomPublicMethod(string $value): void
    {
    }
}
