<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;
use SimpleBus\Message\Recorder\RecordsMessages;

class SomeOtherTestCommandHandler implements MessageHandler
{
    public $commandHandled = false;
    private $messageRecorder;

    public function __construct(RecordsMessages $messageRecorder)
    {
        $this->messageRecorder = $messageRecorder;
    }

    public function handle(Message $command)
    {
        $this->commandHandled = true;

        $this->messageRecorder->record(new SomeOtherEvent());
    }
}
