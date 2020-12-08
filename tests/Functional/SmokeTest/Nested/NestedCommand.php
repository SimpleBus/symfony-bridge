<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested;

final class NestedCommand
{
    public $level;

    public function __construct(int $level = 0)
    {
        $this->level = $level;
    }
}
