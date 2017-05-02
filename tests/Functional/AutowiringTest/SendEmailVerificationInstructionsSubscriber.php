<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\AutowiringTest;

/**
 *
 */
final class SendEmailVerificationInstructionsSubscriber
{
    public $userRegisteredHandled = false;
    public $userChangedEmailHandled = false;

    public function onUserRegistered(UserRegistered $event)
    {
        $this->userRegisteredHandled = true;
    }

    public function onUserChangedEmail(UserChangedEmail $event)
    {
        $this->userChangedEmailHandled = true;
    }
}
