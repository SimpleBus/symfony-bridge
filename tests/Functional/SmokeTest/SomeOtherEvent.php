<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Event\Event;

class SomeOtherEvent implements Event
{
    public function name()
    {
        return 'some_other_event';
    }
}
