<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleBus\Message\Recorder\MessageRecorderCapabilities;
use SimpleBus\Message\Recorder\RecordsMessages;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestEntityCreated;

/**
 * @ORM\Entity
 */
class TestEntity implements RecordsMessages
{
    use MessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    public function __construct()
    {
        $this->record(new TestEntityCreated($this));
    }
}
