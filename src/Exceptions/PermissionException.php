<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Permission
 * 
 * Lancée lorsqu'un utilisateur n'a pas les permissions nécessaires.
 * Code HTTP: 403 Forbidden
 */
class PermissionException extends AppException
{
    protected int $httpCode = 403;
    protected string $errorCode = 'PERMISSION_DENIED';

    private string $permission = '';
    private string $resource = '';
    private string $action = '';

    /**
     * @param string $message Message d'erreur
     * @param string $permission Permission manquante
     * @param string $resource Ressource concernée
     * @param string $action Action tentée
     */
    public function __construct(
        string $message = 'Permission refusée',
        string $permission = '',
        string $resource = '',
        string $action = ''
    ) {
        $details = [];
        
        if ($permission !== '') {
            $details['permission'] = $permission;
            $this->permission = $permission;
        }
        if ($resource !== '') {
            $details['resource'] = $resource;
            $this->resource = $resource;
        }
        if ($action !== '') {
            $details['action'] = $action;
            $this->action = $action;
        }

        parent::__construct($message, 403, 'PERMISSION_DENIED', $details);
    }

    /**
     * Crée une exception pour ressource non autorisée
     */
    public static function forResource(string $resource, string $action): self
    {
        return new self(
            "Vous n'avez pas la permission de {$action} cette ressource: {$resource}",
            "{$resource}.{$action}",
            $resource,
            $action
        );
    }

    /**
     * Crée une exception pour action non autorisée
     */
    public static function forAction(string $action): self
    {
        return new self(
            "Action non autorisée: {$action}",
            $action,
            '',
            $action
        );
    }

    /**
     * Retourne la permission manquante
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * Retourne la ressource concernée
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * Retourne l'action tentée
     */
    public function getAction(): string
    {
        return $this->action;
    }
}
