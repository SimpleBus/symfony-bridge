---
currentMenu: command_bus_bundle
---

# Command bus bundle

Using the building blocks supplied by the [SimpleBus/MessageBus library](https://github.com/SimpleBus/MessageBus) you
can create a command bus, which is basically a message bus, with some middlewares and a map of message handlers. This is
described in the [documentation of MessageBus](http://simplebus.github.io/MessageBus/doc/command_bus.html).

If you use Symfony, you don't have to manually configure a command bus in each project. The [SimpleBus/SymfonyBridge
library](https://github.com/SimpleBus/SymfonyBridge) comes with the `SimpleBusCommandBusBundle` which handles it for
you.

First enable the bundle in your application's kernel:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle()
        )
        ...
    }
    ...
}
```

## Using the command bus

This bundle provides the `command_bus` service which is an instance of `SimpleBus\Message\Bus\MessageBus`. Wherever you
like, you can let it handle commands, e.g. inside a container-aware controller:

```php
// $command is an instance of SimpleBus\Message\Type\Command
$command = ...;

$this->get('command_bus')->handle($command);
```

However, you are encouraged to properly inject the `command_bus` service as a dependency whenever you need it:

```yaml
services:
    some_service:
        arguments:
            - @command_bus
```

## Registering command handlers

As described in the [MessageBus documentation](http://simplebus.github.io/MessageBus/doc/command_bus.html) you can
delegate the handling of particular commands to command handlers. This bundle allows you to register your own
command handlers by adding the `command_handler` tag to the command handler's service definition:

```yaml
services:
    register_user_command_handler:
        class: Fully\Qualified\Class\Name\Of\RegisterUserCommandHandler
        tags:
            - { name: command_handler, handles: Fully\Qualified\Class\Name\Of\RegisterUser }
```

> #### Command handlers are lazy-loaded
>
> Since only one of the command handlers is going to handle any particular command, command handlers are lazy-loaded.
> This means that their services should be defined as public services (i.e. you can't use `public: false` for them).

## Setting the command name resolving strategy

To find the correct command handler for a given command, the name of the command is used. This can be either 1) its
fully-qualified class name (FQCN) or, 2) if the command implements the `SimpleBus\Message\Name\NamedMessage` interface,
the value returned by its static `messageName()` method. By default, the first strategy is used, but you can configure
it in your application configuration:

```yaml
command_bus:
    # default value for this key is "class_based"
    command_name_resolver_strategy: named_message
```

When you change the strategy, you also have to change the value of the `subscribes_to` attribute of your command handler
service definitions:

```yaml
services:
    register_user_command_handler:
        class: Fully\Qualified\Class\Name\Of\RegisterUserCommandHandler
        tags:
            - { name: command_handler, handles: register_user }
```

Make sure that the value of `subscribes_to` matches the return value of `RegisterUser::messageName()`.

## Adding command bus middleware

As described in the [MessageBus documentation](http://simplebus.github.io/MessageBus/doc/command_bus.html) you can
extend the behavior of the command bus by adding middleware to it. This bundle allows you to register your own
middleware by adding the `command_bus_middleware` tag to the middleware service definition:

```yaml
services:
    specialized_command_bus_middleware:
        class: YourSpecializedCommandBusMiddleware
        public: false
        tags:
            - { name: command_bus_middleware, priority: 100 }
```

By providing a value for the `priority` tag attribute you can influence the order in which middlewares are added to the
command bus.

> #### Middlewares are not lazy-loaded
>
> Whenever you use the command bus, you also use all of its middlewares, so command bus middlewares are not lazy-loaded.
> This means that their services should be defined as private services (i.e. you should use `public: false`). See also:
> [Marking Services as public /
> private](http://symfony.com/doc/current/components/dependency_injection/advanced.html#marking-services-as-public-private)
