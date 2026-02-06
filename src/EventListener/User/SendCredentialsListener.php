<?php

declare(strict_types=1);

namespace App\EventListener\User;

use App\Event\User\UserCreatedEvent;
use App\Service\Email\EmailService;

class SendCredentialsListener
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'onUserCreated',
        ];
    }

    public function onUserCreated(UserCreatedEvent $event): void
    {
        try {
            $this->emailService->sendAccountCreated(
                $event->getEmail(),
                $event->getLogin(),
                '' // temporary password is not carried by the event
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'SendCredentialsListener: failed to send credentials email to %s – %s',
                $event->getEmail(),
                $e->getMessage()
            ));
        }
    }
}
