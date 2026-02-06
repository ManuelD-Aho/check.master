<?php

declare(strict_types=1);

namespace App\EventListener\Stage;

use App\Event\Stage\CandidatureRejectedEvent;
use App\Event\Stage\CandidatureSubmittedEvent;
use App\Event\Stage\CandidatureValidatedEvent;
use App\Service\Email\EmailService;
use App\Service\System\SettingsService;

class NotifyCandidatureListener
{
    private EmailService $emailService;
    private SettingsService $settingsService;

    public function __construct(EmailService $emailService, SettingsService $settingsService)
    {
        $this->emailService = $emailService;
        $this->settingsService = $settingsService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CandidatureSubmittedEvent::class => 'onCandidatureSubmitted',
            CandidatureValidatedEvent::class => 'onCandidatureValidated',
            CandidatureRejectedEvent::class  => 'onCandidatureRejected',
        ];
    }

    public function onCandidatureSubmitted(CandidatureSubmittedEvent $event): void
    {
        try {
            $adminEmail = (string) $this->settingsService->get('admin_email', '');

            if ($adminEmail === '') {
                return;
            }

            $this->emailService->send(
                $adminEmail,
                'New candidature submitted',
                'candidature_submitted',
                [
                    'candidatureId' => $event->getCandidatureId(),
                    'etudiantId'    => $event->getEtudiantId(),
                ]
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyCandidatureListener: failed to notify submission for candidature %d – %s',
                $event->getCandidatureId(),
                $e->getMessage()
            ));
        }
    }

    public function onCandidatureValidated(CandidatureValidatedEvent $event): void
    {
        try {
            $this->emailService->sendCandidatureValidated(
                '', // resolved downstream by the email service or caller context
                sprintf('Candidature #%d', $event->getCandidatureId())
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyCandidatureListener: failed to notify validation for candidature %d – %s',
                $event->getCandidatureId(),
                $e->getMessage()
            ));
        }
    }

    public function onCandidatureRejected(CandidatureRejectedEvent $event): void
    {
        try {
            $this->emailService->sendCandidatureRejected(
                '', // resolved downstream by the email service or caller context
                sprintf('Candidature #%d', $event->getCandidatureId()),
                $event->getMotif()
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyCandidatureListener: failed to notify rejection for candidature %d – %s',
                $event->getCandidatureId(),
                $e->getMessage()
            ));
        }
    }
}
