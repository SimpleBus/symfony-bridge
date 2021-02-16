<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use Doctrine\ORM\EntityManager;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity\TestEntity;

class TestCommandHandler
{
    public bool $commandHandled = false;

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(): void
    {
        $this->commandHandled = true;

        $entity = new TestEntity();

        $this->entityManager->persist($entity);

        // flush should be called inside the TransactionalCommandBus
    }
}
