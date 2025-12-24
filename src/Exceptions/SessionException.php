<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Session
 * 
 * Lancée lors d'erreurs liées aux sessions utilisateur.
 */
class SessionException extends AppException
{
    protected int $httpCode = 401;
    protected string $errorCode = 'SESSION_ERROR';

    /**
     * @param string $message Message d'erreur
     * @param string $errorCode Code d'erreur spécifique
     */
    public function __construct(
        string $message = 'Erreur de session',
        string $errorCode = 'SESSION_ERROR'
    ) {
        parent::__construct($message, 401, $errorCode);
    }

    /**
     * Crée une exception pour session expirée
     */
    public static function expired(): self
    {
        return new self(
            'Votre session a expiré. Veuillez vous reconnecter.',
            'SESSION_EXPIRED'
        );
    }

    /**
     * Crée une exception pour session invalide
     */
    public static function invalid(): self
    {
        return new self(
            'Session invalide. Veuillez vous reconnecter.',
            'SESSION_INVALID'
        );
    }

    /**
     * Crée une exception pour session hijackée
     */
    public static function hijacked(): self
    {
        $e = new self(
            'Activité suspecte détectée. Veuillez vous reconnecter.',
            'SESSION_HIJACK_DETECTED'
        );
        $e->httpCode = 403;
        return $e;
    }

    /**
     * Crée une exception pour session verrouillée
     */
    public static function locked(): self
    {
        $e = new self(
            'Votre session est temporairement verrouillée.',
            'SESSION_LOCKED'
        );
        $e->httpCode = 423;
        return $e;
    }

    /**
     * Crée une exception pour connexion depuis un autre appareil
     */
    public static function anotherDevice(): self
    {
        return new self(
            'Vous avez été déconnecté car une connexion a été détectée depuis un autre appareil.',
            'SESSION_REPLACED'
        );
    }

    /**
     * Crée une exception pour nombre maximum de sessions atteint
     */
    public static function maxSessionsReached(): self
    {
        $e = new self(
            'Nombre maximum de sessions simultanées atteint.',
            'MAX_SESSIONS_REACHED'
        );
        $e->httpCode = 429;
        return $e;
    }

    /**
     * Crée une exception pour inactivité
     */
    public static function inactive(): self
    {
        return new self(
            'Session terminée pour cause d\'inactivité.',
            'SESSION_INACTIVE'
        );
    }
}
