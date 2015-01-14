<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Type\Command;

class SomeOtherTestCommand implements Command
{
    public function name()
    {
        return 'some_other_test_command';
    }
}
