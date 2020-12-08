<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoCommandHandlerUsingInvoke
{
    public function __invoke(AutoCommand1 $command)
    {
        $command->setHandled(true);
    }
}
