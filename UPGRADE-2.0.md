# Upgrading from 1.0 to 2.0

## Command and event names

In 1.0 commands and events had a `name()` method which returned their names. This name was also used in the service
tags for command and event handlers. The new default is that commands and events don't have a `name()` method anymore
and their name is just their fully-qualified class name (FQCN):

### Commands and command handlers, events and event handlers

Given this command:

```php
use SimpleBus\Command\Command;

class SomeCommand implements Command
{
    // no name() method anymore
    ...
}
```

You would have a command handler which looks like this:

```php
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;

class SomeCommandHandler implements MessageHandler
{
    public function handle(Message $message)
    {
        ...
    }
}
```

Please note that you shouldn't implement `CommandHandler` anymore. Instead implement `MessageHandler`. The only
parameter of the `handle()` method is not a `Command` anymore, but a `Message`.

You can register this handler like this:

```yaml
services:
    some_command_handler:
        class: Fully\Qualified\Class\Name\Of\SomeCommandHandler
        tags:
            - { name: command_handler, handles: Fully\Qualified\Class\Name\Of\SomeCommand }
```

The same for event subscribers (previously known as event handlers):

```php
use SimpleBus\Event\Event;

class SomeEvent implements Event
{
    ...
}
```

A subscriber for this event might look like this:

```php
use SimpleBus\Message\Subscriber\MessageSubscriber;
use SimpleBus\Message\Message;

class SomeEventSubscriber implements MessageSubscriber
{
    public function notify(Message $message)
    {
        ...
    }
}
```

Please note that you shouldn't implement `EventHandler` anymore, but instead implement `MessageSubscriber`. Also, the
`handle()` method has been renamed to `notify()` and the only parameter is not an `Event`, but a `Message`.

You can register this subscriber like this:


```yaml
services:
    some_event_subscriber:
        class: Fully\Qualified\Class\Name\Of\SomeEventSubscriber
        tags:
            - { name: event_subscriber, handles: Fully\Qualified\Class\Name\Of\SomeEvent }
```

### Named messages

If instead of the FQCN you want to keep using the command/event name as returned by its `messageName()` method, you should
configure this in `config.yml`:

```yaml
command_bus:
    # the name of a command is considered to be its FQCN
    command_name_resolver_strategy: class_based

event_bus:
    # the name of an event should be returned by its messageName() method
    event_name_resolver_strategy: named_message
```

When you choose for the `named_message` strategy, you have to make your commands/events implement the `NamedMessage`
interface:

```php
use SimpleBus\Message\Name\NamedMessage;

class SomeEvent implements Event, NamedMessage
{
    public static function messageName()
    {
        return 'name_of_the_event';
    }
}
```

## Command and event buses have become message bus middlewares

Previously you could define your own command bus and event bus behaviors by implementing `CommandBus` or `EventBus`.
As of version 2.0 you should implement `MessageBusMiddleware` instead:

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
middlewares works just like it did before.

## Event providers have become message recorders

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
`message_recorder`.
