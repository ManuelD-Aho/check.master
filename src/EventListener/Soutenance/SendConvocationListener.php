<?php

declare(strict_types=1);

namespace App\EventListener\Soutenance;

use App\Event\Soutenance\SoutenanceScheduledEvent;
use App\Service\Email\EmailService;

class SendConvocationListener
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SoutenanceScheduledEvent::class => 'onSoutenanceScheduled',
        ];
    }

    public function onSoutenanceScheduled(SoutenanceScheduledEvent $event): void
    {
        try {
            $this->emailService->send(
                '', // jury member emails resolved by the service layer
                'Convocation – Soutenance',
                'soutenance_convocation',
                [
                    'soutenanceId' => $event->getSoutenanceId(),
                    'date'         => $event->getDate(),
                    'heureDebut'   => $event->getHeureDebut(),
                ]
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'SendConvocationListener: failed to send convocation for soutenance %d – %s',
                $event->getSoutenanceId(),
                $e->getMessage()
            ));
        }
    }
}
