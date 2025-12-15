<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Accès Interdit
 * 
 * Lancée quand l'utilisateur est authentifié mais n'a pas les droits.
 * Code HTTP: 403 Forbidden
 */
class ForbiddenException extends AppException
{
    protected int $httpCode = 403;
    protected string $errorCode = 'FORBIDDEN';

    /**
     * @param string $message Message d'erreur
     * @param string $resource Ressource concernée
     * @param string $action Action tentée
     */
    public function __construct(
        string $message = 'Accès non autorisé',
        string $resource = '',
        string $action = ''
    ) {
        $details = [];
        if ($resource !== '') {
            $details['resource'] = $resource;
        }
        if ($action !== '') {
            $details['action'] = $action;
        }

        parent::__construct($message, 403, 'FORBIDDEN', $details);
    }

    /**
     * Permission manquante
     */
    public static function missingPermission(string $resource, string $action): self
    {
        return new self(
            "Vous n'avez pas la permission de {$action} cette ressource",
            $resource,
            $action
        );
    }

    /**
     * Accès à une ressource d'un autre utilisateur
     */
    public static function notOwner(string $resource): self
    {
        return new self(
            "Vous ne pouvez pas accéder à ce(tte) {$resource}",
            $resource,
            'access'
        );
    }

    /**
     * Action non autorisée dans l'état actuel
     */
    public static function invalidState(string $action, string $currentState): self
    {
        $exception = new self(
            "L'action '{$action}' n'est pas autorisée dans l'état actuel",
            '',
            $action
        );
        $exception->details['current_state'] = $currentState;
        return $exception;
    }

    /**
     * Rôle temporaire expiré
     */
    public static function roleExpired(string $role): self
    {
        return new self(
            "Votre rôle temporaire '{$role}' a expiré",
            'role',
            'access'
        );
    }
}
