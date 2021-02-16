<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Recorder\RecordsMessages;

class SomeOtherTestCommandHandler
{
    public bool $commandHandled = false;
    private RecordsMessages $messageRecorder;

    public function __construct(RecordsMessages $messageRecorder)
    {
        $this->messageRecorder = $messageRecorder;
    }

    public function handle(): void
    {
        $this->commandHandled = true;

        $this->messageRecorder->record(new SomeOtherEvent());
    }
}
