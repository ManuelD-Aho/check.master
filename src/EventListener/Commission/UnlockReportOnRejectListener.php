<?php

declare(strict_types=1);

namespace App\EventListener\Commission;

use App\Event\Commission\VoteCompleteEvent;

class UnlockReportOnRejectListener
{
    public static function getSubscribedEvents(): array
    {
        return [
            VoteCompleteEvent::class => 'onVoteComplete',
        ];
    }

    /**
     * Placeholder: actual report unlocking is handled by the service layer.
     * This listener logs the rejection reason when the vote is not accepted.
     */
    public function onVoteComplete(VoteCompleteEvent $event): void
    {
        if ($event->isAccepted()) {
            return;
        }

        error_log(sprintf(
            'UnlockReportOnRejectListener: rapport %d was rejected at %s – unlock pending via service layer',
            $event->getRapportId(),
            $event->getOccurredAt()->format('Y-m-d H:i:s')
        ));
    }
}
