<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Configuration
 * 
 * Lancée lors d'erreurs de configuration système.
 */
class ConfigurationException extends AppException
{
    protected int $httpCode = 500;
    protected string $errorCode = 'CONFIGURATION_ERROR';

    private string $configKey = '';

    /**
     * @param string $message Message d'erreur
     * @param string $configKey Clé de configuration concernée
     */
    public function __construct(
        string $message = 'Erreur de configuration',
        string $configKey = ''
    ) {
        $details = [];
        
        if ($configKey !== '') {
            $details['config_key'] = $configKey;
            $this->configKey = $configKey;
        }

        parent::__construct($message, 500, 'CONFIGURATION_ERROR', $details);
    }

    /**
     * Crée une exception pour clé manquante
     */
    public static function missingKey(string $key): self
    {
        return new self("Configuration manquante: {$key}", $key);
    }

    /**
     * Crée une exception pour valeur invalide
     */
    public static function invalidValue(string $key, string $expectedType): self
    {
        return new self(
            "Valeur de configuration invalide pour '{$key}'. Type attendu: {$expectedType}",
            $key
        );
    }

    /**
     * Crée une exception pour fichier de configuration non trouvé
     */
    public static function fileNotFound(string $filePath): self
    {
        return new self("Fichier de configuration non trouvé: {$filePath}");
    }

    /**
     * Crée une exception pour configuration en lecture seule
     */
    public static function readOnly(string $key): self
    {
        return new self(
            "Cette configuration est en lecture seule: {$key}",
            $key
        );
    }

    /**
     * Crée une exception pour dépendance de configuration
     */
    public static function dependencyMissing(string $key, string $dependsOn): self
    {
        return new self(
            "La configuration '{$key}' nécessite '{$dependsOn}' qui est manquante",
            $key
        );
    }

    /**
     * Retourne la clé de configuration
     */
    public function getConfigKey(): string
    {
        return $this->configKey;
    }
}
