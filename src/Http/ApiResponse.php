<?php

declare(strict_types=1);

namespace Src\Http;

/**
 * Classe ApiResponse
 * 
 * Réponses API uniformes avec structure standardisée:
 * - success: bool
 * - message: string
 * - data: mixed
 * - meta: array (pagination, etc.)
 * - errors: array (si erreur)
 */
class ApiResponse extends Response
{
    /**
     * Structure de base de la réponse
     *
     * @var array<string, mixed>
     */
    protected array $body = [
        'success' => true,
        'message' => '',
        'data' => null,
        'meta' => [],
    ];

    /**
     * Constructeur
     *
     * @param array<string, mixed> $body Corps de la réponse
     * @param int $statusCode Code HTTP
     * @param array<string, string> $headers En-têtes additionnels
     */
    public function __construct(array $body = [], int $statusCode = 200, array $headers = [])
    {
        $this->body = array_merge($this->body, $body);
        $content = json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}';
        
        parent::__construct($content, $statusCode, $headers);
        $this->header('Content-Type', 'application/json; charset=UTF-8');
    }

    /**
     * Crée une réponse de succès
     *
     * @param mixed $data Données à retourner
     * @param string $message Message de succès
     * @param array<string, mixed> $meta Métadonnées optionnelles
     */
    public static function success(mixed $data = null, string $message = 'Opération réussie', array $meta = []): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], 200);
    }

    /**
     * Crée une réponse de création (201)
     *
     * @param mixed $data Données créées
     * @param string $message Message
     */
    public static function created(mixed $data = null, string $message = 'Ressource créée avec succès'): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    /**
     * Crée une réponse sans contenu (204)
     */
    public static function noContent(): self
    {
        $response = new self([], 204);
        $response->setContent('');
        return $response;
    }

    /**
     * Crée une réponse d'erreur
     *
     * @param string $message Message d'erreur
     * @param array<string, mixed> $errors Détails des erreurs
     * @param int $statusCode Code HTTP (défaut: 400)
     */
    public static function error(string $message, array $errors = [], int $statusCode = 400): self
    {
        $body = [
            'success' => false,
            'message' => $message,
            'data' => null,
        ];

        if (!empty($errors)) {
            $body['errors'] = $errors;
        }

        return new self($body, $statusCode);
    }

    /**
     * Crée une réponse d'erreur de validation (422)
     *
     * @param array<string, string> $errors Erreurs par champ
     * @param string $message Message principal
     */
    public static function validationError(array $errors, string $message = 'Les données soumises sont invalides'): self
    {
        return new self([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Crée une réponse 404 Not Found
     */
    public static function notFound(string $message = 'Ressource non trouvée'): self
    {
        return self::error($message, [], 404);
    }

    /**
     * Crée une réponse 401 Unauthorized
     */
    public static function unauthorized(string $message = 'Authentification requise'): self
    {
        return self::error($message, [], 401);
    }

    /**
     * Crée une réponse 403 Forbidden
     */
    public static function forbidden(string $message = 'Accès non autorisé'): self
    {
        return self::error($message, [], 403);
    }

    /**
     * Crée une réponse 429 Too Many Requests
     */
    public static function tooManyRequests(string $message = 'Trop de requêtes', int $retryAfter = 60): self
    {
        $response = self::error($message, ['retry_after' => $retryAfter], 429);
        $response->header('Retry-After', (string) $retryAfter);
        return $response;
    }

    /**
     * Crée une réponse 500 Internal Server Error
     */
    public static function serverError(string $message = 'Une erreur interne est survenue'): self
    {
        return self::error($message, [], 500);
    }

    /**
     * Crée une réponse 503 Service Unavailable
     */
    public static function serviceUnavailable(string $message = 'Service temporairement indisponible'): self
    {
        return self::error($message, [], 503);
    }

    /**
     * Ajoute des métadonnées à la réponse
     *
     * @param array<string, mixed> $meta
     */
    public function withMeta(array $meta): self
    {
        $this->body['meta'] = array_merge($this->body['meta'] ?? [], $meta);
        $this->updateContent();
        return $this;
    }

    /**
     * Ajoute un lien à la réponse
     *
     * @param string $rel Relation du lien
     * @param string $href URL du lien
     */
    public function withLink(string $rel, string $href): self
    {
        if (!isset($this->body['links'])) {
            $this->body['links'] = [];
        }
        $this->body['links'][$rel] = $href;
        $this->updateContent();
        return $this;
    }

    /**
     * Met à jour le contenu JSON
     */
    protected function updateContent(): void
    {
        $this->setContent(json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}');
    }

    /**
     * Retourne le corps de la réponse sous forme de tableau
     *
     * @return array<string, mixed>
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
