<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Name\NamedMessage;
use SimpleBus\Message\Type\Event;

class SomeOtherEvent implements Event, NamedMessage
{
    public static function messageName()
    {
        return 'some_other_event';
    }
}
