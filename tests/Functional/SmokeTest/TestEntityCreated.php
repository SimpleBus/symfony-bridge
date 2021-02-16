<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Name\NamedMessage;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity\TestEntity;

class TestEntityCreated implements NamedMessage
{
    private TestEntity $testEntity;

    public function __construct(TestEntity $testEntity)
    {
        $this->testEntity = $testEntity;
    }

    public function getTestEntity(): TestEntity
    {
        return $this->testEntity;
    }

    public static function messageName(): string
    {
        return 'test_entity_created';
    }
}
