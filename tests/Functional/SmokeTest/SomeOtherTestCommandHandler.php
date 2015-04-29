<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Recorder\RecordsMessages;

class SomeOtherTestCommandHandler
{
    public $commandHandled = false;
    private $messageRecorder;

    public function __construct(RecordsMessages $messageRecorder)
    {
        $this->messageRecorder = $messageRecorder;
    }

    public function handle()
    {
        $this->commandHandled = true;

        $this->messageRecorder->record(new SomeOtherEvent());
    }
}
