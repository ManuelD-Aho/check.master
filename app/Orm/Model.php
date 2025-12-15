<?php

declare(strict_types=1);

namespace App\Orm;

use PDO;
use PDOStatement;

/**
 * Classe abstraite ORM légère pour CheckMaster
 * 
 * Fournit les opérations CRUD basiques avec prepared statements obligatoires.
 * Chaque modèle doit étendre cette classe et définir $table, $primaryKey, $fillable.
 */
abstract class Model
{
    use Relations;

    protected static ?PDO $pdo = null;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Définit la connexion PDO globale
     */
    public static function setConnection(PDO $pdo): void
    {
        static::$pdo = $pdo;
    }

    /**
     * Retourne la connexion PDO
     */
    protected static function getConnection(): PDO
    {
        if (static::$pdo === null) {
            throw new \RuntimeException('Connexion base de données non configurée');
        }
        return static::$pdo;
    }

    /**
     * Remplit les attributs depuis un tableau
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable, true) || $key === $this->primaryKey) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Getter magique pour les attributs
     */
    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Setter magique pour les attributs
     */
    public function __set(string $name, mixed $value): void
    {
        if (in_array($name, $this->fillable, true) || $name === $this->primaryKey) {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * Vérifie si un attribut existe
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Retourne la valeur de la clé primaire
     */
    public function getId(): ?int
    {
        $id = $this->attributes[$this->primaryKey] ?? null;
        return $id !== null ? (int) $id : null;
    }

    /**
     * Retourne tous les attributs
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Trouve un enregistrement par sa clé primaire
     */
    public static function find(int $id): ?static
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = :id LIMIT 1";
        $stmt = static::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new static($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne tous les enregistrements
     */
    public static function all(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}";
        $stmt = static::getConnection()->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new static($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Recherche avec conditions WHERE
     * 
     * @param array $conditions ['column' => 'value'] ou ['column' => ['operator', 'value']]
     */
    public static function where(array $conditions): array
    {
        $instance = new static();
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                [$operator, $val] = $value;
                $whereClauses[] = "{$column} {$operator} :{$column}";
                $params[$column] = $val;
            } else {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
        }

        $sql = "SELECT * FROM {$instance->table} WHERE " . implode(' AND ', $whereClauses);
        $stmt = static::getConnection()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new static($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Trouve le premier enregistrement correspondant aux conditions
     */
    public static function firstWhere(array $conditions): ?static
    {
        $results = static::where($conditions);
        return $results[0] ?? null;
    }

    /**
     * Sauvegarde l'enregistrement (INSERT ou UPDATE)
     */
    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        }
        return $this->insert();
    }

    /**
     * Insère un nouvel enregistrement
     */
    protected function insert(): bool
    {
        $data = array_filter(
            $this->attributes,
            fn($key) => in_array($key, $this->fillable, true),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($data)) {
            return false;
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = static::getConnection()->prepare($sql);
        $result = $stmt->execute($data);

        if ($result) {
            $this->attributes[$this->primaryKey] = (int) static::getConnection()->lastInsertId();
            $this->exists = true;
        }

        return $result;
    }

    /**
     * Met à jour un enregistrement existant
     */
    protected function update(): bool
    {
        $id = $this->getId();
        if ($id === null) {
            return false;
        }

        $data = array_filter(
            $this->attributes,
            fn($key) => in_array($key, $this->fillable, true),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($data)) {
            return false;
        }

        $setClauses = array_map(fn($col) => "{$col} = :{$col}", array_keys($data));
        $data['__id'] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) .
            " WHERE {$this->primaryKey} = :__id";
        $stmt = static::getConnection()->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Supprime l'enregistrement
     */
    public function delete(): bool
    {
        $id = $this->getId();
        if ($id === null || !$this->exists) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = static::getConnection()->prepare($sql);
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            $this->exists = false;
        }

        return $result;
    }

    /**
     * Compte le nombre d'enregistrements
     */
    public static function count(array $conditions = []): int
    {
        $instance = new static();

        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) FROM {$instance->table}";
            $stmt = static::getConnection()->query($sql);
        } else {
            $whereClauses = [];
            $params = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql = "SELECT COUNT(*) FROM {$instance->table} WHERE " . implode(' AND ', $whereClauses);
            $stmt = static::getConnection()->prepare($sql);
            $stmt->execute($params);
        }

        return (int) $stmt->fetchColumn();
    }

    /**
     * Exécute une requête SQL brute avec prepared statement
     */
    public static function raw(string $sql, array $params = []): PDOStatement
    {
        $stmt = static::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Démarre une transaction
     */
    public static function beginTransaction(): bool
    {
        return static::getConnection()->beginTransaction();
    }

    /**
     * Valide une transaction
     */
    public static function commit(): bool
    {
        return static::getConnection()->commit();
    }

    /**
     * Annule une transaction
     */
    public static function rollBack(): bool
    {
        return static::getConnection()->rollBack();
    }
}
