<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Event\Event;
use SimpleBus\Message\NamedMessage;

class SomeOtherEvent implements Event, NamedMessage
{
    public function name()
    {
        return 'some_other_event';
    }
}
