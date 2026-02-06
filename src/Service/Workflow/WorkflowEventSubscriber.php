<?php
declare(strict_types=1);

namespace App\Service\Workflow;

use Psr\EventDispatcher\EventDispatcherInterface;
use App\Service\System\AuditService;

class WorkflowEventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;
    private AuditService $auditService;

    public function __construct(EventDispatcherInterface $eventDispatcher, AuditService $auditService)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->auditService = $auditService;
    }

    public function onTransition(
        string $workflowName,
        string $transition,
        string $fromState,
        string $toState,
        ?int $entityId = null
    ): void {
        $action = sprintf('workflow.%s.%s', $workflowName, $transition);

        $this->auditService->log(
            $action,
            'succes',
            null,
            $workflowName,
            $entityId,
            ['state' => $fromState],
            ['state' => $toState],
            sprintf('Transition "%s" from "%s" to "%s"', $transition, $fromState, $toState)
        );
    }

    public function getSubscribedEvents(): array
    {
        return [];
    }
}
