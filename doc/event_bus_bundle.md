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
// $event is an instance of SimpleBus\Message\Type\Event
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
            - { name: event_subscriber, handles: Fully\Qualified\Class\Name\Of\UserRegistered }
```

> #### Event subscribers are lazy-loaded
>
> Since only some of the event subscribers are going to handle any particular event, event subscribers are lazy-loaded.
> This means that their services should be defined as public services (i.e. you can't use `public: false` for them).

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
