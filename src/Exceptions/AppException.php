<?php

declare(strict_types=1);

namespace Src\Exceptions;

use Exception;
use Throwable;

/**
 * Exception de base pour CheckMaster
 * 
 * Toutes les exceptions personnalisées doivent étendre cette classe.
 * Fournit une structure uniforme pour la gestion des erreurs.
 */
class AppException extends Exception
{
    protected int $httpCode = 500;
    protected array $details = [];
    protected string $errorCode = 'INTERNAL_ERROR';

    /**
     * @param string $message Message d'erreur
     * @param int $httpCode Code HTTP (défaut: 500)
     * @param string $errorCode Code d'erreur interne
     * @param array $details Détails supplémentaires
     * @param Throwable|null $previous Exception précédente
     */
    public function __construct(
        string $message = 'Une erreur interne est survenue',
        int $httpCode = 500,
        string $errorCode = 'INTERNAL_ERROR',
        array $details = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $httpCode, $previous);
        $this->httpCode = $httpCode;
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    /**
     * Retourne le code HTTP
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * Retourne le code d'erreur interne
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Retourne les détails de l'erreur
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * Ajoute un détail
     */
    public function addDetail(string $key, mixed $value): self
    {
        $this->details[$key] = $value;
        return $this;
    }

    /**
     * Convertit l'exception en tableau pour réponse JSON
     */
    public function toArray(): array
    {
        $data = [
            'error' => true,
            'code' => $this->errorCode,
            'message' => $this->getMessage(),
        ];

        if (!empty($this->details)) {
            $data['details'] = $this->details;
        }

        return $data;
    }

    /**
     * Convertit l'exception en JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Crée une réponse HTTP avec l'exception
     */
    public function respond(): void
    {
        http_response_code($this->httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo $this->toJson();
    }

    /**
     * Factory pour créer une exception depuis un message simple
     */
    public static function fromMessage(string $message, int $httpCode = 500): static
    {
        return new static($message, $httpCode);
    }
}
