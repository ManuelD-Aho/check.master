<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Orm\Model;
use PDO;
use PDOException;
use Src\Exceptions\DatabaseException;

/**
 * Service Database
 * 
 * Gère la connexion à la base de données et fournit des utilitaires SQL.
 * Utilise PDO avec prepared statements obligatoires.
 * 
 * @see Constitution I - Database-Driven Architecture
 */
class ServiceDatabase
{
    private static ?PDO $connection = null;

    private const DEFAULT_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ];

    /**
     * Initialise la connexion à la base de données
     *
     * @throws DatabaseException Si la connexion échoue
     */
    public static function connect(
        string $host,
        string $database,
        string $username,
        string $password,
        int $port = 3306,
        array $options = []
    ): PDO {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            $mergedOptions = array_merge(self::DEFAULT_OPTIONS, $options);
            self::$connection = new PDO($dsn, $username, $password, $mergedOptions);
            Model::setConnection(self::$connection);
            return self::$connection;
        } catch (PDOException $e) {
            throw DatabaseException::connectionFailed($e->getMessage());
        }
    }

    /**
     * Initialise depuis les variables d'environnement
     *
     * @throws DatabaseException
     */
    public static function connectFromEnv(): PDO
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $database = getenv('DB_DATABASE') ?: 'checkmaster';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        $port = (int) (getenv('DB_PORT') ?: 3306);

        return self::connect($host, $database, $username, $password, $port);
    }

    /**
     * Retourne la connexion active
     *
     * @throws DatabaseException Si non connecté
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            throw DatabaseException::notConnected();
        }
        return self::$connection;
    }

    /**
     * Vérifie si une connexion est active
     */
    public static function isConnected(): bool
    {
        return self::$connection !== null;
    }

    /**
     * Ferme la connexion
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }

    /**
     * Démarre une transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Valide une transaction
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Annule une transaction
     */
    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Vérifie si une transaction est active
     */
    public static function inTransaction(): bool
    {
        return self::getConnection()->inTransaction();
    }

    /**
     * Exécute une requête avec prepared statement
     *
     * @return \PDOStatement
     * @throws DatabaseException
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw DatabaseException::queryFailed($sql, $e->getMessage());
        }
    }

    /**
     * Exécute une requête et retourne toutes les lignes
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Exécute une requête et retourne une seule ligne
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Exécute une requête et retourne une seule valeur
     */
    public static function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        return self::query($sql, $params)->fetchColumn($column);
    }

    /**
     * Exécute une requête INSERT et retourne l'ID inséré
     *
     * @throws DatabaseException
     */
    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        self::query($sql, $data);

        return (int) self::getConnection()->lastInsertId();
    }

    /**
     * Exécute une requête UPDATE
     *
     * @throws DatabaseException
     */
    public static function update(string $table, array $data, array $where): int
    {
        $setClauses = [];
        foreach (array_keys($data) as $col) {
            $setClauses[] = "{$col} = :set_{$col}";
        }

        $whereClauses = [];
        foreach (array_keys($where) as $col) {
            $whereClauses[] = "{$col} = :where_{$col}";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $setClauses) .
               " WHERE " . implode(' AND ', $whereClauses);

        $params = [];
        foreach ($data as $key => $value) {
            $params["set_{$key}"] = $value;
        }
        foreach ($where as $key => $value) {
            $params["where_{$key}"] = $value;
        }

        return self::query($sql, $params)->rowCount();
    }

    /**
     * Exécute une requête DELETE
     *
     * @throws DatabaseException
     */
    public static function delete(string $table, array $where): int
    {
        $whereClauses = [];
        foreach (array_keys($where) as $col) {
            $whereClauses[] = "{$col} = :{$col}";
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereClauses);

        return self::query($sql, $where)->rowCount();
    }

    /**
     * Compte les enregistrements
     */
    public static function count(string $table, array $where = []): int
    {
        if (empty($where)) {
            $sql = "SELECT COUNT(*) FROM {$table}";
            return (int) self::fetchColumn($sql);
        }

        $whereClauses = [];
        foreach (array_keys($where) as $col) {
            $whereClauses[] = "{$col} = :{$col}";
        }

        $sql = "SELECT COUNT(*) FROM {$table} WHERE " . implode(' AND ', $whereClauses);
        return (int) self::fetchColumn($sql, $where);
    }

    /**
     * Vérifie si un enregistrement existe
     */
    public static function exists(string $table, array $where): bool
    {
        return self::count($table, $where) > 0;
    }

    /**
     * Retourne le dernier ID inséré
     */
    public static function lastInsertId(): int
    {
        return (int) self::getConnection()->lastInsertId();
    }

    /**
     * Exécute une fonction dans une transaction
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     * @throws DatabaseException|\Throwable
     */
    public static function transaction(callable $callback): mixed
    {
        self::beginTransaction();

        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (\Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    /**
     * Échappe un identifiant SQL (table, colonne)
     */
    public static function escapeIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /**
     * Ping la base de données pour vérifier la connexion
     */
    public static function ping(): bool
    {
        try {
            self::getConnection()->query('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retourne les informations sur la base de données
     */
    public static function info(): array
    {
        $pdo = self::getConnection();

        return [
            'server_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'client_version' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
            'driver_name' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            'connection_status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
        ];
    }
}
