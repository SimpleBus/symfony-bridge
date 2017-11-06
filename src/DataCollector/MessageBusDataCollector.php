<?php

namespace SimpleBus\SymfonyBridge\DataCollector;

use SimpleBus\Message\Name\NamedMessage;
use SimpleBus\SymfonyBridge\Logger\MessageLogger;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\SymfonyBridge\Bus\BusRegistry;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageBusDataCollector extends DataCollector
{
    private $logger;
    private $busRegistry;

    public function __construct(MessageLogger $logger, BusRegistry $busRegistry)
    {
        $this->logger = $logger;
        $this->busRegistry = $busRegistry;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $messages = array_map(function($logEntry) {
            $message = $logEntry->getMessage();

            return [
                'bus' => $logEntry->getBusName(),
                'messageClass' => $message instanceOf NamedMessage ? $message->messageName() : get_class($message),
                'timestamp' => $logEntry->getTimestamp()
            ];
        }, $this->logger->getLogs());

        $buses = [];
        foreach ($this->busRegistry->all() as $name => $bus) {
            $busData = [
                'name' => $name
            ];

            if ($bus instanceof MessageBusSupportingMiddleware) {
                $busData['middlewares'] = array_map('get_class', $bus->getMiddlewares());
            }

            $buses[] = $busData;
        }

        $this->data = array(
            'messages' => $messages,
            'buses' => $buses,
        );
    }

    public function getMessages()
    {
        return $this->data['messages'];
    }

    public function getBuses()
    {
        return $this->data['buses'];
    }

    public function getTotalNumMessages()
    {
        return count($this->data['messages']);
    }

    public function getName()
    {
        return 'simple_bus';
    }
}
