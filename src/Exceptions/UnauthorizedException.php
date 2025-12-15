<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Non Autorisé
 * 
 * Lancée quand l'authentification est requise mais absente ou invalide.
 * Code HTTP: 401 Unauthorized
 */
class UnauthorizedException extends AppException
{
    protected int $httpCode = 401;
    protected string $errorCode = 'UNAUTHORIZED';

    /**
     * @param string $message Message d'erreur
     * @param string $errorCode Code d'erreur spécifique
     */
    public function __construct(
        string $message = 'Authentification requise',
        string $errorCode = 'UNAUTHORIZED'
    ) {
        parent::__construct($message, 401, $errorCode);
    }

    /**
     * Session expirée
     */
    public static function sessionExpired(): self
    {
        return new self(
            'Votre session a expiré. Veuillez vous reconnecter.',
            'SESSION_EXPIRED'
        );
    }

    /**
     * Token invalide
     */
    public static function invalidToken(): self
    {
        return new self(
            'Token d\'authentification invalide',
            'INVALID_TOKEN'
        );
    }

    /**
     * Identifiants incorrects
     */
    public static function invalidCredentials(): self
    {
        return new self(
            'Email ou mot de passe incorrect',
            'INVALID_CREDENTIALS'
        );
    }

    /**
     * Compte verrouillé
     */
    public static function accountLocked(int $minutesRemaining = 0): self
    {
        $message = $minutesRemaining > 0
            ? "Compte temporairement verrouillé. Réessayez dans {$minutesRemaining} minute(s)."
            : 'Compte temporairement verrouillé.';

        $exception = new self($message, 'ACCOUNT_LOCKED');
        $exception->details['minutes_remaining'] = $minutesRemaining;
        return $exception;
    }

    /**
     * Compte inactif
     */
    public static function accountInactive(): self
    {
        return new self(
            'Ce compte est désactivé. Contactez l\'administrateur.',
            'ACCOUNT_INACTIVE'
        );
    }
}
