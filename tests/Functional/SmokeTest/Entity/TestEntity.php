<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestEntityCreated;

/**
 * @ORM\Entity
 */
class TestEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public int $id;

    public function __construct()
    {
        $this->record(new TestEntityCreated($this));
    }
}
