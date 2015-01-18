---
currentMenu: upgrade_guide
---

# Upgrade guide

## From 1.0 to 2.0

### Commands

Before:

```php
use SimpleBus\Command\Command;

class FooCommand implements Command
{
    public function name()
    {
        return 'foo';
    }
}
```

After:

```php
use SimpleBus\Message\Type\Command;

class FooCommand implements Command
{
    // no name() method anymore
}
```

Or:

```php
use SimpleBus\Message\Type\Command;
use SimpleBus\Message\Name\NamedMessage;

class FooCommand implements Command, NamedMessage
{
    public static function messageName()
    {
        return 'foo';
    }
}
```

See below for more information about this change.

### Events

Before:

```php
use SimpleBus\Event\Event;

class BarEvent implements Event
{
    public function name()
    {
        return 'bar';
    }
}
```

After:

```php
use SimpleBus\Message\Type\Event;

class BarEvent implements Event
{
    // no name() method anymore
}
```

Or:

```php
use SimpleBus\Message\Type\Event;
use SimpleBus\Message\Name\NamedMessage;

class BarEvent implements Event, NamedMessage
{
    public static function messageName()
    {
        return 'bar';
    }
}
```

See below for more information about this change.

### Command handlers

Before:

```php
use SimpleBus\Command\Handler\CommandHandler;
use SimpleBus\Command\Command;

class FooCommandHandler implements CommandHandler
{
    public function handle(Command $command)
    {
        ...
    }
}
```

After:

```php
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;

class FooCommandHandler implements MessageHandler
{
    public function handle(Message $command)
    {
        ...
    }
}
```

You can register this handler like this:

```yaml
services:
    foo_command_handler:
        class: Fully\Qualified\Class\Name\Of\FooCommandHandler
        tags:
            - { name: command_handler, handles: Fully\Qualified\Class\Name\Of\FooCommand }
```

Or, if you let commands implement `NamedMessage`:

```yaml
services:
    foo_command_handler:
        class: Fully\Qualified\Class\Name\Of\FooCommandHandler
        tags:
            - { name: command_handler, handles: foo }
```

### Event subscribers

Before:

```php
use SimpleBus\Event\Handler\EventHandler;
use SimpleBus\Event\Event;

class BarEventHandler implements EventHandler
{
    public function handle(Event $event)
    {
        ...
    }
}
```

After:

```php
use SimpleBus\Message\Subscriber\MessageSubscriber;
use SimpleBus\Message\Message;

class BarEventSubscriber implements MessageSubscriber
{
    public function notify(Message $message)
    {
        ...
    }
}
```

You can register this subscriber like this:

```yaml
services:
    bar_event_subscriber:
        class: Fully\Qualified\Class\Name\Of\BarEventSubscriber
        tags:
            - { name: event_subscriber, handles: Fully\Qualified\Class\Name\Of\BarEvent }
```

Or, if you let events implement `NamedMessage`:

```yaml
services:
    bar_event_subscriber:
        class: Fully\Qualified\Class\Name\Of\BarEventSubscriber
        tags:
            - { name: event_subscriber, handles: bar }
```

### Named messages

If instead of the FQCN you want to keep using the command/event name as returned by its `messageName()` method, you
should configure this in `config.yml`:

```yaml
command_bus:
    # the name of a command is considered to be its FQCN
    command_name_resolver_strategy: class_based

event_bus:
    # the name of an event should be returned by its messageName() method
    event_name_resolver_strategy: named_message
```

This strategy then applies to all your commands or events.

### Command and event bus middlewares

Previously you could define your own command bus and event bus behaviors by implementing `CommandBus` or `EventBus`.
As of version 2.0 in both cases you should implement `MessageBusMiddleware` instead:

```php
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

class SpecializedCommandBusMiddleware implements MessageBusMiddleware
{
    public function handle(Message $message, callable $next)
    {
        // do whatever you want

        $next($message);

        // maybe do some more things
    }
}
```

Please note that the trait `RemembersNext` doesn't exist anymore. Instead of calling `$this->next()` you should now
call `$next($message)`.

You should register command bus middleware like this:

```yaml
services:
    specialized_command_bus_middleware:
        class: Fully\Qualified\Class\Name\Of\SpecializedCommandBusMiddleware
        tags:
            - { name: command_bus_middleware, priority: 0 }
```

The same for event bus middleware, but then you should use the tag `event_bus_middleware`. The priority value for
middlewares works just like it did before. Read more in the [CommandBusBundle](command_bus_bundle.md) and
[EventBusBundle](event_bus_bundle.md) documentation.

## Event providers have become event recorders

If you have entities that collect domain events, you should implement `ContainsRecordedMessages` instead of
`ProvidesEvents` and use the trait `PrivateMessageRecorderCapabilities` instead of `EventProviderCapabilities`. The
`raise()` method has been renamed to `record()`.

```php
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

class Entity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    public function someFunction()
    {
        // $event is an instance of Message
        $event = ...;

        $this->record($event);
    }
}
```

If you had registered event providers using the service tag `event_provider`, you should change that to
`event_recorder`.

Read more about event recorders in the [EventBusBundle](event_bus_bundle.md) documentation.
