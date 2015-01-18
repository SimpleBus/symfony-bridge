---
currentMenu: doctrine_orm_bridge_bundle
---

# Doctrine ORM and domain events

As described in the documentation of the [SimpleBus/DoctrineORMBridge
package](https://github.com/SimpleBus/DoctrineORMBridge) library it provides:

- A command bus middleware which [wraps the handling of commands inside a database
transaction](http://simplebus.github.io/DoctrineORMBridge/doc/transactions.md)
- A command bus middleware which [collects domain events recorded by entities and lets the event bus handle
them](http://simplebus.github.io/DoctrineORMBridge/doc/domain_events.md)

When you enable the `DoctrineORMBridgeBundle` in your project, both features will be automatically registered as
command bus middlewares:

```php

```

Both middlewares use the `default` entity manager.
