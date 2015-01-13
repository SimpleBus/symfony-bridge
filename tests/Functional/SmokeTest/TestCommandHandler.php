<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use Doctrine\ORM\EntityManager;
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity\TestEntity;

class TestCommandHandler implements MessageHandler
{
    public $commandHandled = false;

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Message $command)
    {
        $this->commandHandled = true;

        $entity = new TestEntity();

        $this->entityManager->persist($entity);

        // flush should be called inside the TransactionalCommandBus
    }
}
