<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\AutowiringTest;

/**
 *
 */
final class UserRegistered
{
    private $userId;

    /**
     * @param int $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }
}
