<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity;

use Doctrine\ORM\Mapping as ORM;
use SimpleBus\Event\Provider\EventProviderCapabilities;
use SimpleBus\Event\Provider\ProvidesEvents;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestEntityCreated;

/**
 * @ORM\Entity
 */
class TestEntity implements ProvidesEvents
{
    use EventProviderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    public function __construct()
    {
        $this->raise(new TestEntityCreated($this));
    }
}
