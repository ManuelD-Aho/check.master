<?php

declare(strict_types=1);

namespace Src\Support;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;

/**
 * Factory pour la création d'instances de logger
 * 
 * Utilise Monolog avec différents handlers selon les besoins:
 * - Fichiers rotatifs pour la production
 * - Console pour le développement
 * - Handler personnalisé pour la table pister
 */
class LoggerFactory
{
    private static array $loggers = [];
    private static string $logDir = '';
    private static string $defaultLevel = 'debug';

    /**
     * Retourne un logger pour un canal donné
     */
    public static function get(string $channel = 'app'): LoggerInterface
    {
        if (!isset(self::$loggers[$channel])) {
            self::$loggers[$channel] = self::create($channel);
        }

        return self::$loggers[$channel];
    }

    /**
     * Crée un nouveau logger pour un canal
     */
    public static function create(string $channel): Logger
    {
        $logger = new Logger($channel);

        // Ajouter le handler fichier rotatif
        $logger->pushHandler(self::createFileHandler($channel));

        // Ajouter les processors
        $logger->pushProcessor(new IntrospectionProcessor());

        if (PHP_SAPI !== 'cli') {
            $logger->pushProcessor(new WebProcessor());
        }

        return $logger;
    }

    /**
     * Crée un handler fichier rotatif
     */
    private static function createFileHandler(string $channel): RotatingFileHandler
    {
        $logDir = self::$logDir ?: self::getDefaultLogDir();
        $path = $logDir . "/{$channel}.log";

        $handler = new RotatingFileHandler(
            filename: $path,
            maxFiles: 30, // 30 jours de rétention
            level: self::getLogLevel()
        );

        $handler->setFormatter(self::createFormatter());

        return $handler;
    }

    /**
     * Crée le formateur de logs
     */
    private static function createFormatter(): LineFormatter
    {
        $format = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $dateFormat = 'Y-m-d H:i:s';

        return new LineFormatter($format, $dateFormat, true, true);
    }

    /**
     * Retourne le niveau de log depuis la configuration
     */
    private static function getLogLevel(): int
    {
        $level = self::$defaultLevel;

        return match (strtolower($level)) {
            'debug' => Logger::DEBUG,
            'info' => Logger::INFO,
            'notice' => Logger::NOTICE,
            'warning' => Logger::WARNING,
            'error' => Logger::ERROR,
            'critical' => Logger::CRITICAL,
            'alert' => Logger::ALERT,
            'emergency' => Logger::EMERGENCY,
            default => Logger::DEBUG,
        };
    }

    /**
     * Retourne le répertoire de logs par défaut
     */
    private static function getDefaultLogDir(): string
    {
        $rootDir = dirname(__DIR__, 3);
        $logDir = $rootDir . '/storage/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        return $logDir;
    }

    /**
     * Configure le répertoire de logs
     */
    public static function setLogDirectory(string $directory): void
    {
        self::$logDir = $directory;
        self::$loggers = []; // Reset tous les loggers
    }

    /**
     * Configure le niveau de log par défaut
     */
    public static function setDefaultLevel(string $level): void
    {
        self::$defaultLevel = $level;
        self::$loggers = [];
    }

    /**
     * Logger pour les erreurs applicatives
     */
    public static function error(): LoggerInterface
    {
        return self::get('error');
    }

    /**
     * Logger pour l'authentification
     */
    public static function auth(): LoggerInterface
    {
        return self::get('auth');
    }

    /**
     * Logger pour les requêtes SQL
     */
    public static function sql(): LoggerInterface
    {
        return self::get('sql');
    }

    /**
     * Logger pour l'audit
     */
    public static function audit(): LoggerInterface
    {
        return self::get('audit');
    }

    /**
     * Logger pour les performances
     */
    public static function performance(): LoggerInterface
    {
        return self::get('performance');
    }

    /**
     * Logger pour les notifications
     */
    public static function notification(): LoggerInterface
    {
        return self::get('notification');
    }

    /**
     * Réinitialise tous les loggers (pour les tests)
     */
    public static function reset(): void
    {
        self::$loggers = [];
        self::$logDir = '';
        self::$defaultLevel = 'debug';
    }

    /**
     * Log rapide d'une info
     */
    public static function info(string $message, array $context = []): void
    {
        self::get('app')->info($message, $context);
    }

    /**
     * Log rapide d'une erreur
     */
    public static function logError(string $message, array $context = []): void
    {
        self::get('error')->error($message, $context);
    }

    /**
     * Log rapide d'un warning
     */
    public static function warning(string $message, array $context = []): void
    {
        self::get('app')->warning($message, $context);
    }
}
