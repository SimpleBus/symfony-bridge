# SymfonyBridge

[![Build Status](https://travis-ci.org/SimpleBus/SymfonyBridge.svg?branch=master)](https://travis-ci.org/SimpleBus/SymfonyBridge)

By [Matthias Noback](http://php-and-symfony.matthiasnoback.nl/)

## Installation

Using Composer:

    composer require simple-bus/symfony-bridge

Just enable the Symfony bundles you need in your Symfony project:

- `SimpleBusCommandBusBundle` to enable basic command bus functionality
- `SimpleBusEventBusBundle` to enable basic event bus functionality (requires `SimpleBusCommandBusBundle` to be enabled too)
- `SimpleBusDoctrineOrmBridgeBundle` to enable the Doctrine ORM bridge (requires `SimpleBusCommandBusBundle` and `SimpleBusEventBusBundle` to be enabled)

## Usage

Read more about using commands and command buses, events and event buses in the documentation of the dedicated
libraries:

- [CommandBus](https://github.com/SimpleBus/CommandBus)
- [EventBus](https://github.com/SimpleBus/EventBus)
- [DoctrineORMBridge](https://github.com/SimpleBus/DoctrineORMBridge)

## Symfony-specific usage

### Use the command bus in a controller

```php
<?php

class UserController extends Controller
{
    public function registerAction()
    {
        $command = new RegisterUserCommand(...);

        $this->get('command_bus')->handle($command);

        ...
    }
}
```

### Register command handlers using a service tag

```yaml
services:
    register_user_command_handler:
        class: Matthias\App\RegisterUserCommandHandler
        tags:
            - { name: command_handler, handles: register_user }
```

Make sure the value in the `handles` attribute of the `command_handler` tag matches the value returned by (in this case)
`RegisterUserCommand::name()`.

### Doctrine ORM and domain events

Whenever you do something with entities in your command handler, the changes will be automatically persisted afterwards.
Entities that were involved in the transaction will be asked to release their events:

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use SimpleBus\Event\Provider\ProvidesEvents;
use SimpleBus\Event\Provider\EventProviderCapabilities;
use Matthias\App\Event\TestEntityCreated;

/**
 * @ORM\Entity
 */
class User implements ProvidesEvents
{
    use EventProviderCapabilities;

    public function __construct()
    {
        $this->raise(new UserRegisteredEvent($this));
    }
}
```

When the `flush` operation was successful, the events stored by the entity will be released. Each of the events will
be handled by event handlers.

### Register event subscribers using a service tag:

```yaml
    notification_mail_event_handler:
        class: Matthias\App\SendNotificationMailWhenUserRegistered
        tags:
            - { name: event_subscriber, subscribes_to: user_registered }
```

Make sure the value of the `handles`  attribute is the same as the value returned by (in this case)
`UserRegisteredEvent::name()`.

## Extension points

### Custom command buses

It's possible to influence the behavior of the command bus or the event bus by registering services which are
 instances of `MessageBusMiddleware`:

```php
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\Message\Message;

class SpecializedMiddleware implements MessageBusMiddleware
{
    public function handle(Message $message, callable $next)
    {
        // do anything you like

        // then let the next middleware do its job
        $next($message);

        // finally, you are allowed to do some other things
    }
}
```

Register the service using the service tag `command_bus_middleware` or `event_bus_middleware`, to add it as
middleware to the `command_bus` and the `event_bus` service respectively:

```yaml
services:
    specialized_middleware:
        class: SpecializedMiddleware
        tags:
            - { name: command_bus_middleware, priority: 100 }
```

By providing a value for the `priority` tag attribute you can influence the order in which middlewares like this are
added to the command or event bus.

Some interesting features you can implement using specialized middlewares:

- Handle commands/events asynchronously (for better performance)
- Log commands/events (for debugging)
- Store events (which will be DDD-CQRS-cool!)
- Dispatch Symfony events before and after handling a command
- ...
