<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Auto;

final class AutoEvent1
{
    /**
     * @var class-string[]
     */
    private array $handled = [];

    /**
     * @param class-string $subscriber
     */
    public function isHandledBy(string $subscriber): bool
    {
        return in_array($subscriber, $this->handled);
    }

    public function setHandledBy(object $subscriber): void
    {
        $this->handled[] = get_class($subscriber);
    }
}
