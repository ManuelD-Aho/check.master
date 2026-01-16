<?php

declare(strict_types=1);

namespace Src\Database;

use PDO;
use PDOException;
use Src\Exceptions\DatabaseException;

/**
 * Connection Pool Manager pour gestion optimale des connexions DB
 * 
 * Fonctionnalités:
 * - Pool de connexions réutilisables
 * - Gestion automatique du cycle de vie des connexions
 * - Connexions read/write séparées (master/replica)
 * - Health checks et reconnexion automatique
 * - Statistiques d'utilisation
 * - Timeout et limite de connexions
 * 
 * @package Src\Database
 */
class ConnectionPool
{
    private array $config;
    private array $connections = [];
    private array $availableConnections = [];
    private array $busyConnections = [];
    private array $stats = [
        'total_created' => 0,
        'total_reused' => 0,
        'total_closed' => 0,
        'current_active' => 0,
        'max_reached' => 0
    ];
    
    private int $maxConnections;
    private int $minConnections;
    private int $connectionTimeout;
    private int $idleTimeout;
    private int $maxLifetime;
    private bool $healthCheckEnabled;

    /**
     * Constructeur
     *
     * @param array $config Configuration du pool
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->maxConnections = $config['pool']['max_connections'] ?? 10;
        $this->minConnections = $config['pool']['min_connections'] ?? 2;
        $this->connectionTimeout = $config['pool']['connection_timeout'] ?? 5;
        $this->idleTimeout = $config['pool']['idle_timeout'] ?? 600; // 10 minutes
        $this->maxLifetime = $config['pool']['max_lifetime'] ?? 3600; // 1 heure
        $this->healthCheckEnabled = $config['pool']['health_check'] ?? true;

        // Initialiser le pool minimum
        $this->initializePool();
    }

    /**
     * Initialiser le pool avec le nombre minimum de connexions
     *
     * @return void
     * @throws DatabaseException
     */
    private function initializePool(): void
    {
        for ($i = 0; $i < $this->minConnections; $i++) {
            try {
                $connection = $this->createConnection();
                $this->availableConnections[] = $connection;
            } catch (DatabaseException $e) {
                // Log l'erreur mais continue
                error_log("Échec création connexion pool: " . $e->getMessage());
            }
        }
    }

    /**
     * Obtenir une connexion du pool
     *
     * @param string $type Type de connexion (write, read)
     * @return PDO
     * @throws DatabaseException
     */
    public function getConnection(string $type = 'write'): PDO
    {
        // Nettoyer les connexions expirées
        $this->cleanupExpiredConnections();

        // Chercher une connexion disponible
        $connection = $this->findAvailableConnection($type);

        if ($connection !== null) {
            $this->stats['total_reused']++;
            return $connection;
        }

        // Créer une nouvelle connexion si le maximum n'est pas atteint
        if ($this->getTotalConnections() < $this->maxConnections) {
            $connection = $this->createConnection($type);
            $this->stats['total_created']++;
            $this->stats['current_active']++;
            $this->stats['max_reached'] = max(
                $this->stats['max_reached'],
                $this->stats['current_active']
            );

            return $connection;
        }

        // Attendre qu'une connexion se libère
        return $this->waitForConnection($type);
    }

    /**
     * Chercher une connexion disponible
     *
     * @param string $type Type de connexion
     * @return PDO|null
     */
    private function findAvailableConnection(string $type): ?PDO
    {
        foreach ($this->availableConnections as $index => $connData) {
            // Vérifier si la connexion est toujours valide
            if ($this->isConnectionValid($connData)) {
                // Retirer de available et ajouter à busy
                unset($this->availableConnections[$index]);
                $this->availableConnections = array_values($this->availableConnections);
                
                $connData['last_used'] = time();
                $connData['in_use'] = true;
                $this->busyConnections[] = $connData;

                return $connData['connection'];
            }

            // Connexion invalide, la fermer
            $this->closeConnection($connData);
            unset($this->availableConnections[$index]);
        }

        $this->availableConnections = array_values($this->availableConnections);
        return null;
    }

    /**
     * Attendre qu'une connexion se libère
     *
     * @param string $type Type de connexion
     * @return PDO
     * @throws DatabaseException
     */
    private function waitForConnection(string $type): PDO
    {
        $startTime = time();
        
        while (time() - $startTime < $this->connectionTimeout) {
            // Vérifier si une connexion s'est libérée
            $connection = $this->findAvailableConnection($type);
            if ($connection !== null) {
                $this->stats['total_reused']++;
                return $connection;
            }

            // Attendre un court moment
            usleep(100000); // 100ms
        }

        throw new DatabaseException(
            "Timeout: Aucune connexion disponible après {$this->connectionTimeout}s"
        );
    }

    /**
     * Créer une nouvelle connexion
     *
     * @param string $type Type de connexion (write, read)
     * @return PDO
     * @throws DatabaseException
     */
    private function createConnection(string $type = 'write'): PDO
    {
        try {
            // Déterminer les credentials selon le type
            $config = $type === 'read' && isset($this->config['read'])
                ? $this->config['read']
                : $this->config;

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'] ?? 'localhost',
                $config['port'] ?? 3306,
                $config['database'],
                $config['charset'] ?? 'utf8mb4'
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false, // Désactivé pour le pool
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $options
            );

            // Stocker les métadonnées de connexion
            $connData = [
                'connection' => $pdo,
                'type' => $type,
                'created_at' => time(),
                'last_used' => time(),
                'in_use' => true,
                'query_count' => 0
            ];

            $this->busyConnections[] = $connData;

            return $pdo;

        } catch (PDOException $e) {
            throw new DatabaseException(
                "Erreur de connexion à la base de données: " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Libérer une connexion (la rendre disponible)
     *
     * @param PDO $connection Connexion à libérer
     * @return void
     */
    public function releaseConnection(PDO $connection): void
    {
        foreach ($this->busyConnections as $index => $connData) {
            if ($connData['connection'] === $connection) {
                // Retirer de busy
                unset($this->busyConnections[$index]);
                $this->busyConnections = array_values($this->busyConnections);

                // Vérifier si la connexion est toujours valide
                if ($this->isConnectionValid($connData)) {
                    // Ajouter à available
                    $connData['in_use'] = false;
                    $connData['last_used'] = time();
                    $this->availableConnections[] = $connData;
                } else {
                    // Fermer la connexion invalide
                    $this->closeConnection($connData);
                    $this->stats['current_active']--;
                }

                return;
            }
        }
    }

    /**
     * Vérifier si une connexion est valide
     *
     * @param array $connData Données de connexion
     * @return bool
     */
    private function isConnectionValid(array $connData): bool
    {
        $now = time();

        // Vérifier le temps de vie maximum
        if ($now - $connData['created_at'] > $this->maxLifetime) {
            return false;
        }

        // Vérifier le timeout d'inactivité
        if (!$connData['in_use'] && $now - $connData['last_used'] > $this->idleTimeout) {
            return false;
        }

        // Health check si activé
        if ($this->healthCheckEnabled) {
            try {
                $connData['connection']->query('SELECT 1');
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Nettoyer les connexions expirées
     *
     * @return void
     */
    private function cleanupExpiredConnections(): void
    {
        $now = time();

        // Nettoyer les connexions disponibles
        foreach ($this->availableConnections as $index => $connData) {
            if (!$this->isConnectionValid($connData)) {
                $this->closeConnection($connData);
                unset($this->availableConnections[$index]);
                $this->stats['current_active']--;
            }
        }

        $this->availableConnections = array_values($this->availableConnections);

        // Ne garder que le minimum de connexions disponibles
        $excessCount = count($this->availableConnections) - $this->minConnections;
        if ($excessCount > 0) {
            for ($i = 0; $i < $excessCount; $i++) {
                $connData = array_shift($this->availableConnections);
                $this->closeConnection($connData);
                $this->stats['current_active']--;
            }
        }
    }

    /**
     * Fermer une connexion
     *
     * @param array $connData Données de connexion
     * @return void
     */
    private function closeConnection(array $connData): void
    {
        try {
            // PDO se ferme automatiquement quand la référence est détruite
            unset($connData['connection']);
            $this->stats['total_closed']++;
        } catch (\Exception $e) {
            error_log("Erreur fermeture connexion: " . $e->getMessage());
        }
    }

    /**
     * Obtenir le nombre total de connexions
     *
     * @return int
     */
    private function getTotalConnections(): int
    {
        return count($this->availableConnections) + count($this->busyConnections);
    }

    /**
     * Obtenir les statistiques du pool
     *
     * @return array
     */
    public function getStats(): array
    {
        return array_merge($this->stats, [
            'available' => count($this->availableConnections),
            'busy' => count($this->busyConnections),
            'total' => $this->getTotalConnections(),
            'max_allowed' => $this->maxConnections
        ]);
    }

    /**
     * Fermer toutes les connexions
     *
     * @return void
     */
    public function closeAll(): void
    {
        // Fermer les connexions disponibles
        foreach ($this->availableConnections as $connData) {
            $this->closeConnection($connData);
        }

        // Fermer les connexions occupées
        foreach ($this->busyConnections as $connData) {
            $this->closeConnection($connData);
        }

        $this->availableConnections = [];
        $this->busyConnections = [];
        $this->stats['current_active'] = 0;
    }

    /**
     * Vérifier la santé du pool
     *
     * @return array
     */
    public function healthCheck(): array
    {
        $healthy = 0;
        $unhealthy = 0;

        foreach ($this->availableConnections as $connData) {
            if ($this->isConnectionValid($connData)) {
                $healthy++;
            } else {
                $unhealthy++;
            }
        }

        foreach ($this->busyConnections as $connData) {
            if ($this->isConnectionValid($connData)) {
                $healthy++;
            } else {
                $unhealthy++;
            }
        }

        return [
            'status' => $unhealthy === 0 ? 'healthy' : 'degraded',
            'healthy_connections' => $healthy,
            'unhealthy_connections' => $unhealthy,
            'total_connections' => $healthy + $unhealthy
        ];
    }

    /**
     * Réinitialiser les statistiques
     *
     * @return void
     */
    public function resetStats(): void
    {
        $this->stats = [
            'total_created' => 0,
            'total_reused' => 0,
            'total_closed' => 0,
            'current_active' => $this->stats['current_active'],
            'max_reached' => 0
        ];
    }

    /**
     * Destructeur - ferme toutes les connexions
     */
    public function __destruct()
    {
        $this->closeAll();
    }
}
