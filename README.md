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
- [CommandEventBridge](https://github.com/SimpleBus/CommandEventBridge)
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

### Register event handlers using a service tag:

```yaml
    notification_mail_event_handler:
        class: Matthias\App\SendNotificationMailWhenUserRegistered
        tags:
            - { name: event_handler, handles: user_registered }
```

Make sure the value of the `handles`  attribute is the same as the value returned by (in this case)
`UserRegisteredEvent::name()`.

## Extension points

### Custom command buses

It's possible to wrap command buses and add functionality before or after calling the "next" command bus to handle a command. Use the service tag `command_bus` and optionally extend the `RemembersNext` command bus.

- Handle commands asynchronously (for better performance)
- Log commands (for debugging)
- Dispatch Symfony events before and after handling a command

### Custom event buses

It's possible to wrap event buses and add functionality before or after calling the "next" event bus to handle an event. Use the service tag `event_bus` and optionally extend the `RemembersNext` event bus.

Just some ideas for custom event buses:

- Store events (which will be DDD-CQRS-cool!)
- Handle events asynchronously (for better performance)
- Log events (for debugging)
