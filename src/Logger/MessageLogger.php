<?php

namespace SimpleBus\SymfonyBridge\Logger;

class MessageLogger
{
    const LIMIT_ACTION_THROW_EXCEPTION = 1;
    const LIMIT_ACTION_DISCARD_AND_STOP = 2;

    private $logLimit;
    private $limitAction;

    private $messages;
    private $numMessages = 0;

    private $enabled = true;

    public function __construct($logLimit = 100, $limitAction = self::LIMIT_ACTION_THROW_EXCEPTION)
    {
        $this->logLimit = $logLimit;
        $this->limitAction = $limitAction;
        $this->messages = [];
    }

    public function logMessage($message, $busName)
    {
        if (!$this->enabled) {
            return;
        }

        $this->numMessages++;
        if ($this->numMessages > $this->logLimit) {
            if ($this->limitAction == self::LIMIT_ACTION_THROW_EXCEPTION) {
                throw new \RuntimeException("Message log is full");
            } elseif (self::LIMIT_ACTION_DISCARD_AND_STOP) {
                $this->messages = [];
                $this->enabled = false;
                return;
            }
        }

        $this->messages[] = new LogEntry($message, $busName);
    }

    public function getLogs()
    {
        return $this->messages;
    }
}
