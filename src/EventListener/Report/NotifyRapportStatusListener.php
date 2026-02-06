<?php

declare(strict_types=1);

namespace App\EventListener\Report;

use App\Event\Report\RapportApprovedEvent;
use App\Event\Report\RapportReturnedEvent;
use App\Event\Report\RapportSubmittedEvent;
use App\Service\Email\EmailService;

class NotifyRapportStatusListener
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RapportSubmittedEvent::class => 'onRapportSubmitted',
            RapportApprovedEvent::class  => 'onRapportApproved',
            RapportReturnedEvent::class  => 'onRapportReturned',
        ];
    }

    public function onRapportSubmitted(RapportSubmittedEvent $event): void
    {
        try {
            $this->emailService->send(
                '', // recipient resolved by the service layer
                'Report submitted',
                'rapport_submitted',
                [
                    'rapportId'  => $event->getRapportId(),
                    'etudiantId' => $event->getEtudiantId(),
                ]
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyRapportStatusListener: failed to notify submission for rapport %d – %s',
                $event->getRapportId(),
                $e->getMessage()
            ));
        }
    }

    public function onRapportApproved(RapportApprovedEvent $event): void
    {
        try {
            $this->emailService->sendRapportApproved(
                '', // recipient resolved by the service layer
                sprintf('Rapport #%d', $event->getRapportId())
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyRapportStatusListener: failed to notify approval for rapport %d – %s',
                $event->getRapportId(),
                $e->getMessage()
            ));
        }
    }

    public function onRapportReturned(RapportReturnedEvent $event): void
    {
        try {
            $this->emailService->sendRapportReturned(
                '', // recipient resolved by the service layer
                sprintf('Rapport #%d', $event->getRapportId()),
                $event->getMotif()
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyRapportStatusListener: failed to notify return for rapport %d – %s',
                $event->getRapportId(),
                $e->getMessage()
            ));
        }
    }
}
