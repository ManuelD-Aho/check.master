<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Workflow
 * 
 * Lancée pour les erreurs liées aux transitions d'état du workflow.
 * Code HTTP: 422 Unprocessable Entity
 */
class WorkflowException extends AppException
{
    protected int $httpCode = 422;
    protected string $errorCode = 'WORKFLOW_ERROR';

    /**
     * @param string $message Message d'erreur
     * @param string $currentState État actuel
     * @param string $attemptedAction Action tentée
     */
    public function __construct(
        string $message = 'Erreur de workflow',
        string $currentState = '',
        string $attemptedAction = ''
    ) {
        $details = [];
        if ($currentState !== '') {
            $details['current_state'] = $currentState;
        }
        if ($attemptedAction !== '') {
            $details['attempted_action'] = $attemptedAction;
        }

        parent::__construct($message, 422, 'WORKFLOW_ERROR', $details);
    }

    /**
     * Transition non autorisée
     */
    public static function invalidTransition(string $from, string $to, ?string $reason = null): self
    {
        $message = $reason !== null
            ? "Transition de '{$from}' vers '{$to}' non autorisée: {$reason}"
            : "Transition de '{$from}' vers '{$to}' non autorisée";

        $exception = new self($message, $from, 'transition');
        $exception->details['target_state'] = $to;
        if ($reason !== null) {
            $exception->details['reason'] = $reason;
        }
        return $exception;
    }

    /**
     * Prérequis non remplis
     */
    public static function prerequisiteNotMet(string $state, string $prerequisite): self
    {
        return new self(
            "Prérequis non rempli pour l'état '{$state}': {$prerequisite}",
            $state,
            'check_prerequisites'
        );
    }

    /**
     * Délai dépassé
     */
    public static function deadlineExceeded(string $state, int $daysOver): self
    {
        $exception = new self(
            "Le délai pour l'état '{$state}' est dépassé de {$daysOver} jour(s)",
            $state,
            'check_deadline'
        );
        $exception->details['days_over'] = $daysOver;
        return $exception;
    }

    /**
     * Rôle non autorisé pour cette transition
     */
    public static function roleNotAuthorized(string $role, string $transition): self
    {
        $exception = new self(
            "Le rôle '{$role}' n'est pas autorisé à effectuer la transition '{$transition}'",
            '',
            $transition
        );
        $exception->details['role'] = $role;
        return $exception;
    }

    /**
     * État terminal atteint
     */
    public static function terminalState(string $state): self
    {
        return new self(
            "L'état '{$state}' est un état terminal. Aucune transition possible.",
            $state,
            'transition'
        );
    }

    /**
     * Dossier déjà dans cet état
     */
    public static function alreadyInState(string $state): self
    {
        return new self(
            "Le dossier est déjà dans l'état '{$state}'",
            $state,
            'transition'
        );
    }
}
