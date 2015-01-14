<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\NamedMessage;
use SimpleBus\Message\Type\Event;

class SomeOtherEvent implements Event, NamedMessage
{
    public function name()
    {
        return 'some_other_event';
    }
}
