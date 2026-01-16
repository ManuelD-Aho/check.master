<?php

declare(strict_types=1);

namespace Src\Database;

use PDO;
use Src\Exceptions\DatabaseException;

/**
 * Classe DB - Façade pour accès simplifié à la base de données
 * 
 * Fonctionnalités:
 * - Singleton pour instance globale
 * - Query Builder intégré
 * - Gestion des transactions
 * - Support Connection Pool
 * - Méthodes helper pour requêtes communes
 * 
 * @package Src\Database
 */
class DB
{
    private static ?ConnectionPool $pool = null;
    private static ?PDO $connection = null;
    private static int $transactionLevel = 0;

    /**
     * Initialiser la connexion avec configuration
     *
     * @param array $config Configuration DB
     * @return void
     */
    public static function initialize(array $config): void
    {
        if ($config['pool']['enabled'] ?? false) {
            self::$pool = new ConnectionPool($config);
        } else {
            self::$connection = self::createSimpleConnection($config);
        }
    }

    /**
     * Créer une connexion simple (sans pool)
     *
     * @param array $config Configuration
     * @return PDO
     * @throws DatabaseException
     */
    private static function createSimpleConnection(array $config): PDO
    {
        try {
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
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            return new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (\PDOException $e) {
            throw new DatabaseException(
                "Erreur connexion DB: " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Obtenir l'instance PDO actuelle
     *
     * @return PDO
     * @throws DatabaseException
     */
    public static function connection(): PDO
    {
        if (self::$pool !== null) {
            return self::$pool->getConnection();
        }

        if (self::$connection === null) {
            throw new DatabaseException("Database non initialisée. Appeler DB::initialize() d'abord.");
        }

        return self::$connection;
    }

    /**
     * Créer une nouvelle instance de Query Builder
     *
     * @param string $table Nom de la table
     * @return QueryBuilder
     */
    public static function table(string $table): QueryBuilder
    {
        return (new QueryBuilder(self::connection()))->table($table);
    }

    /**
     * Démarrer une transaction
     *
     * @return bool
     * @throws DatabaseException
     */
    public static function beginTransaction(): bool
    {
        if (self::$transactionLevel === 0) {
            $result = self::connection()->beginTransaction();
            if ($result) {
                self::$transactionLevel++;
            }
            return $result;
        }

        // Savepoint pour transactions imbriquées
        self::connection()->exec("SAVEPOINT trans" . self::$transactionLevel);
        self::$transactionLevel++;

        return true;
    }

    /**
     * Valider une transaction
     *
     * @return bool
     * @throws DatabaseException
     */
    public static function commit(): bool
    {
        if (self::$transactionLevel === 0) {
            throw new DatabaseException("Aucune transaction active");
        }

        if (self::$transactionLevel === 1) {
            $result = self::connection()->commit();
            if ($result) {
                self::$transactionLevel = 0;
            }
            return $result;
        }

        // Release savepoint
        self::$transactionLevel--;
        self::connection()->exec("RELEASE SAVEPOINT trans" . self::$transactionLevel);

        return true;
    }

    /**
     * Annuler une transaction
     *
     * @return bool
     * @throws DatabaseException
     */
    public static function rollBack(): bool
    {
        if (self::$transactionLevel === 0) {
            throw new DatabaseException("Aucune transaction active");
        }

        if (self::$transactionLevel === 1) {
            $result = self::connection()->rollBack();
            if ($result) {
                self::$transactionLevel = 0;
            }
            return $result;
        }

        // Rollback to savepoint
        self::$transactionLevel--;
        self::connection()->exec("ROLLBACK TO SAVEPOINT trans" . self::$transactionLevel);

        return true;
    }

    /**
     * Exécuter un callback dans une transaction
     *
     * @param callable $callback Callback à exécuter
     * @return mixed Résultat du callback
     * @throws DatabaseException
     */
    public static function transaction(callable $callback)
    {
        self::beginTransaction();

        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (\Exception $e) {
            self::rollBack();
            throw $e;
        }
    }

    /**
     * Exécuter une requête SELECT
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return array
     * @throws DatabaseException
     */
    public static function select(string $sql, array $bindings = []): array
    {
        try {
            $stmt = self::connection()->prepare($sql);
            $stmt->execute($bindings);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            throw new DatabaseException(
                "Erreur SELECT: " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Exécuter une requête SELECT et retourner une seule ligne
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return object|null
     * @throws DatabaseException
     */
    public static function selectOne(string $sql, array $bindings = []): ?object
    {
        $results = self::select($sql, $bindings);
        return $results[0] ?? null;
    }

    /**
     * Exécuter une requête INSERT
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return int ID inséré
     * @throws DatabaseException
     */
    public static function insert(string $sql, array $bindings = []): int
    {
        try {
            $stmt = self::connection()->prepare($sql);
            $stmt->execute($bindings);
            return (int) self::connection()->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException(
                "Erreur INSERT: " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Exécuter une requête UPDATE
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return int Nombre de lignes affectées
     * @throws DatabaseException
     */
    public static function update(string $sql, array $bindings = []): int
    {
        return self::statement($sql, $bindings);
    }

    /**
     * Exécuter une requête DELETE
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return int Nombre de lignes supprimées
     * @throws DatabaseException
     */
    public static function delete(string $sql, array $bindings = []): int
    {
        return self::statement($sql, $bindings);
    }

    /**
     * Exécuter une requête générique
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return int Nombre de lignes affectées
     * @throws DatabaseException
     */
    public static function statement(string $sql, array $bindings = []): int
    {
        try {
            $stmt = self::connection()->prepare($sql);
            $stmt->execute($bindings);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new DatabaseException(
                "Erreur requête: " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Obtenir une valeur unique
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return mixed
     * @throws DatabaseException
     */
    public static function scalar(string $sql, array $bindings = [])
    {
        $result = self::selectOne($sql, $bindings);
        if ($result) {
            $values = get_object_vars($result);
            return reset($values);
        }
        return null;
    }

    /**
     * Obtenir les statistiques du pool (si activé)
     *
     * @return array|null
     */
    public static function getPoolStats(): ?array
    {
        return self::$pool?->getStats();
    }

    /**
     * Health check de la connexion
     *
     * @return bool
     */
    public static function ping(): bool
    {
        try {
            self::connection()->query('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtenir le dernier ID inséré
     *
     * @return int
     */
    public static function lastInsertId(): int
    {
        return (int) self::connection()->lastInsertId();
    }

    /**
     * Échapper une valeur pour utilisation dans SQL brut (déconseillé)
     *
     * @param string $value Valeur
     * @return string
     */
    public static function escape(string $value): string
    {
        return self::connection()->quote($value);
    }
}
