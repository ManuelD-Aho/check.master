<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Ressource Non Trouvée
 * 
 * Lancée quand une ressource demandée n'existe pas.
 * Code HTTP: 404 Not Found
 */
class NotFoundException extends AppException
{
    protected int $httpCode = 404;
    protected string $errorCode = 'NOT_FOUND';

    /**
     * @param string $resource Type de ressource (ex: 'Utilisateur', 'Etudiant')
     * @param int|string|null $id Identifiant recherché
     */
    public function __construct(
        string $resource = 'Ressource',
        int|string|null $id = null
    ) {
        $message = $id !== null
            ? "{$resource} avec l'identifiant '{$id}' non trouvé(e)"
            : "{$resource} non trouvé(e)";

        parent::__construct(
            $message,
            404,
            'NOT_FOUND',
            ['resource' => $resource, 'id' => $id]
        );
    }

    /**
     * Factory pour un modèle non trouvé
     */
    public static function model(string $modelClass, int|string $id): self
    {
        $parts = explode('\\', $modelClass);
        $resourceName = end($parts);
        return new self($resourceName, $id);
    }

    /**
     * Factory pour une route non trouvée
     */
    public static function route(string $path): self
    {
        $exception = new self('Route');
        $exception->details['path'] = $path;
        return $exception;
    }

    /**
     * Factory pour un fichier non trouvé
     */
    public static function file(string $path): self
    {
        return new self('Fichier', $path);
    }
}
