<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested;

final class PostExecutionRecord
{
    /** @var int */
    public $level;

    public function __construct(int $level)
    {
        $this->level = $level;
    }
}
