<?php

namespace SimpleBus\SymfonyBridge\ConsoleCommand;

use ReflectionClass;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;

class HandleMessageCommand extends Command
{
    protected $messageBus;

    public function setMessageBus(MessageBus $messageBus)
    {
        $this->messageBus = $commandBus;
    }

    protected function configure()
    {
        $this
            ->addArgument('message_class', InputArgument::REQUIRED, "The FQCN of the message.")
            ->addArgument('arguments', InputArgument::IS_ARRAY, "Any constructor arguments to pass.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $messageClass = $input->getArgument('message_class');
        $arguments = $input->getArgument('arguments');

        $rc = new ReflectionClass($messageClass);
        $message = $rc->newInstanceArgs($arguments);

        $this->messageBus->handle($message);
    }
}
