<?php

declare(strict_types=1);

namespace App\EventListener\Commission;

use App\Event\Commission\VoteCompleteEvent;
use App\Event\Commission\VoteSubmittedEvent;
use App\Service\Email\EmailService;

class NotifyVoteProgressListener
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VoteSubmittedEvent::class => 'onVoteSubmitted',
            VoteCompleteEvent::class  => 'onVoteComplete',
        ];
    }

    public function onVoteSubmitted(VoteSubmittedEvent $event): void
    {
        try {
            $this->emailService->sendCommissionNotification(
                [], // recipients resolved by the service layer
                sprintf(
                    'Vote submitted for rapport %d by member %d – decision: %s',
                    $event->getRapportId(),
                    $event->getMembreId(),
                    $event->getDecision()
                )
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyVoteProgressListener: failed to notify vote for rapport %d – %s',
                $event->getRapportId(),
                $e->getMessage()
            ));
        }
    }

    public function onVoteComplete(VoteCompleteEvent $event): void
    {
        try {
            $decision = $event->isAccepted() ? 'accepted' : 'rejected';

            $this->emailService->sendCommissionNotification(
                [], // recipients resolved by the service layer
                sprintf(
                    'Vote complete for rapport %d – result: %s',
                    $event->getRapportId(),
                    $decision
                )
            );
        } catch (\Throwable $e) {
            error_log(sprintf(
                'NotifyVoteProgressListener: failed to notify vote result for rapport %d – %s',
                $event->getRapportId(),
                $e->getMessage()
            ));
        }
    }
}
