<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Event\Event;
use SimpleBus\Message\NamedMessage;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity\TestEntity;

class TestEntityCreated implements Event, NamedMessage
{
    private $testEntity;

    public function __construct(TestEntity $testEntity)
    {
        $this->testEntity = $testEntity;
    }

    public function getTestEntity()
    {
        return $this->testEntity;
    }

    public function name()
    {
        return 'test_entity_created';
    }
}
