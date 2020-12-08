<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested;

final class PreExecutionRecord
{
    /** @var int */
    public $level;

    public function __construct(int $level)
    {
        $this->level = $level;
    }
}
