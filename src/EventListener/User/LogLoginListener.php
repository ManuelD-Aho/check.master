<?php

declare(strict_types=1);

namespace App\EventListener\User;

use App\Event\User\UserLoginEvent;
use App\Service\System\AuditService;

class LogLoginListener
{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserLoginEvent::class => 'onUserLogin',
        ];
    }

    public function onUserLogin(UserLoginEvent $event): void
    {
        $this->auditService->logLogin(
            $event->getUserId(),
            $event->isSuccessful(),
            $event->getIpAddress()
        );
    }
}
