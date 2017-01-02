<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use SimpleBus\SymfonyBridge\Tests\Functional\AutowiringTest\SendEmailVerificationInstructionsSubscriber;
use SimpleBus\SymfonyBridge\Tests\Functional\AutowiringTest\UserChangedEmail;
use SimpleBus\SymfonyBridge\Tests\Functional\AutowiringTest\UserRegistered;

/**
 *
 */
final class AutowiringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_registers_and_handlers_autowired_subscribers()
    {
        $kernel = new TestKernel('test', true, 'autowiring', __DIR__ . '/AutowiringTest/config.yml');
        $kernel->boot();
        $container = $kernel->getContainer();

        $eventBus = $container->get('event_bus');
        $eventBus->handle(new UserRegistered(42));
        $eventBus->handle(new UserChangedEmail(42));

        $subscriber = $container->get('test_event_subscriber');
        /** @var $subscriber SendEmailVerificationInstructionsSubscriber */

        $this->assertTrue($subscriber->userRegisteredHandled);
        $this->assertTrue($subscriber->userChangedEmailHandled);
    }
}
