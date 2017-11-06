<?php

namespace SimpleBus\SymfonyBridge\Logger;

class LogEntry
{
    private $message;
    private $busName;
    private $timestamp;

    public function __construct($message, $busName)
    {
        $this->message = $message;
        $this->busName = $busName;
        $this->timestamp = microtime(true);
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getBusName()
    {
        return $this->busName;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
