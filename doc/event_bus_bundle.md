---
currentMenu: event_bus_bundle
---

# Event bus bundle

Using the building blocks supplied by the [SimpleBus/MessageBus library](https://github.com/SimpleBus/MessageBus) you
can create an event bus, which is basically a message bus, with some middlewares and a collection of message
subscribers. This is described in the [documentation of
MessageBus](http://simplebus.github.io/MessageBus/doc/event_bus.html).

If you use Symfony, you don't have to manually configure an event bus in each project. The [SimpleBus/SymfonyBridge
library](https://github.com/SimpleBus/SymfonyBridge) package comes with the `SimpleBusEventBusBundle` which handles it
for you.

First enable the bundle in your application's kernel:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new SimpleBus\SymfonyBridge\SimpleBusEventBusBundle()
        )
        ...
    }
    ...
}
```

## Using the event bus

This bundle provides the `event_bus` service which is an instance of `MessageBus`. Wherever you like, you can let it
handle events, e.g. by fetching it inside a container-aware controller:

```php
// $event is an arbitrary object that will be passed to the event subscriber
$event = ...;

$this->get('event_bus')->handle($event);
```

However, you are encouraged to properly inject the `event_bus` service as a dependency whenever you need it:

```yaml
services:
    some_service:
        arguments:
            - @event_bus
```

## Registering event subscribers

As described in the [MessageBus documentation](http://simplebus.github.io/MessageBus/doc/event_bus.html) you can
notify event subscribers about the occurrence of a particular event. This bundle allows you to register your own
event subscribers by adding the `event_subscriber` tag to the event subscriber's service definition:

```yaml
services:
    user_registered_event_subscriber:
        class: Fully\Qualified\Class\Name\Of\UserRegisteredEventSubscriber
        tags:
            - { name: event_subscriber, subscribes_to: Fully\Qualified\Class\Name\Of\UserRegistered }
```

> #### Event subscribers are lazy-loaded
>
> Since only some of the event subscribers are going to handle any particular event, event subscribers are lazy-loaded.
> This means that their services should be defined as public services (i.e. you can't use `public: false` for them).


> #### Event subscribers are callables
>
> Any service that is a [PHP callable](http://php.net/manual/en/language.types.callable.php) itself can be used as an
> event subscriber. If a service itself is not callable, SimpleBus looks for a `notify` method and calls it. If you want
> to use a custom method, just add a `method` attribute to the `event_subscriber` tag:
>
> ```yaml
> services:
>     user_registered_event_subscriber:
>         ...
>         tags:
>             - { name: event_subscriber, subscribes_to: ..., method: userRegistered }
```

## Setting the event name resolving strategy

To find the correct event subscribers for a given event, the name of the event is used. This can be either 1) its fully-
qualified class name (FQCN) or, 2) if the event implements the `SimpleBus\Message\Name\NamedMessage` interface, the
value returned by its static `messageName()` method. By default, the first strategy is used, but you can configure it
in your application configuration:

```yaml
event_bus:
    # default value for this key is "class_based"
    event_name_resolver_strategy: named_message
```

When you change the strategy, you also have to change the value of the `subscribes_to` attribute of your event
subscriber service definitions:

```yaml
services:
    user_registered_event_subscriber:
        class: Fully\Qualified\Class\Name\Of\UserRegisteredEventSubscriber
        tags:
            - { name: event_subscriber, subscribes_to: user_registered }
```

Make sure that the value of `subscribes_to` matches the return value of `UserRegistered::messageName()`.

## Adding event bus middlewares

As described in the [MessageBus documentation](http://simplebus.github.io/MessageBus/doc/event_bus.html) you can
extend the behavior of the event bus by adding middlewares to it. This bundle allows you to register your own
middlewares by adding the `event_bus_middleware` tag to middleware service definitions:

```yaml
services:
    specialized_event_bus_middleware:
        class: YourSpecializedEventBusMiddleware
        public: false
        tags:
            - { name: event_bus_middleware, priority: 100 }
```

By providing a value for the `priority` tag attribute you can influence the order in which middlewares are added to the
event bus.

> #### Middlewares are not lazy-loaded
>
> Whenever you use the event bus, you also use all of its middlewares, so event bus middlewares are not lazy-loaded.
> This means that their services should be defined as private services (i.e. you should use `public: false`). See also:
> [Marking Services as public /
> private](http://symfony.com/doc/current/components/dependency_injection/advanced.html#marking-services-as-public-private)

## Event recorders

### Recording events

As explained [in the documentation of MessageBus](http://simplebus.github.io/MessageBus/doc/message_recorder.html) you
can collect events while a command is being handled. If you want to record new events you can inject the
`event_recorder` service as a constructor argument of a command handler:

```php
use SimpleBus\Message\Recorder\RecordsMessages;

class SomeInterestingCommandHandler
{
    private $eventRecorder;

    public function __construct(RecordsMessages $eventRecorder)
    {
        $this->eventRecorder = $eventRecorder;
    }

    public function handle($command)
    {
        ...

        // create an event
        $event = new SomethingInterestingHappened();

        // record the event
        $this->eventRecorder->record($event);
    }
}
```

The corresponding service definition looks like this:

```yaml
services:
    some_interesting_command_handler:
    arguments:
        - @event_recorder
    tags:
        - { name: command_handler, handles: Fully\Qualified\Name\Of\SomeInterestingCommand
```

Recorded events will be handled after the command has been completely handled.

### Registering your own message recorders

In case you have another source for recorded message (for instance a class that collects domain events like the
[DoctrineORMBridge](https://github.com/SimpleBus/DoctrineORMBridge) does), you can register it as a message recorder:

```php
use SimpleBus\Message\Recorder\ContainsRecordedMessages;

class PropelDomainEvents implements ContainsRecordedMessages
{
    public function recordedMessages()
    {
        // return an array of Message instances
    }

    public function eraseRecordedMessages()
    {
        // clear the internal array containing the recorded messages
    }
}
```

The corresponding service definition looks like this:

```yaml
services:
    propel_domain_events:
        class: Fully\Qualified\Class\Name\Of\PropelDomainEvents
        public: false
        tags:
            - { name: event_recorder }
```

> ## Logging
>
> If you want to log every event that is being handled, enable logging in `config.yml`:
>
> ```yaml
> event_bus:
>     logging: ~
> ```
>
> Messages will be logged to the `event_bus` channel.
