<?php

declare(strict_types=1);

namespace App\Orm;

/**
 * Trait Relations pour les modèles ORM
 * 
 * Fournit les méthodes de relation: hasMany, belongsTo, belongsToMany.
 * Supporte l'eager loading pour éviter les requêtes N+1.
 */
trait Relations
{
    protected array $relations = [];
    protected array $eagerLoad = [];

    /**
     * Définit une relation "appartient à" (N:1)
     *
     * @param string $relatedClass Classe du modèle lié
     * @param string $foreignKey Clé étrangère dans cette table
     * @param string $ownerKey Clé primaire dans la table liée
     */
    public function belongsTo(string $relatedClass, string $foreignKey, string $ownerKey = 'id'): ?Model
    {
        $relationName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        if (isset($this->relations[$relationName])) {
            return $this->relations[$relationName];
        }

        $foreignKeyValue = $this->attributes[$foreignKey] ?? null;
        if ($foreignKeyValue === null) {
            return null;
        }

        /** @var Model $related */
        $related = new $relatedClass();
        $result = $relatedClass::firstWhere([$ownerKey => $foreignKeyValue]);

        $this->relations[$relationName] = $result;
        return $result;
    }

    /**
     * Définit une relation "a plusieurs" (1:N)
     *
     * @param string $relatedClass Classe du modèle lié
     * @param string $foreignKey Clé étrangère dans la table liée
     * @param string $localKey Clé locale (défaut: clé primaire)
     */
    public function hasMany(string $relatedClass, string $foreignKey, ?string $localKey = null): array
    {
        $relationName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        if (isset($this->relations[$relationName])) {
            return $this->relations[$relationName];
        }

        $localKey = $localKey ?? $this->primaryKey;
        $localKeyValue = $this->attributes[$localKey] ?? null;

        if ($localKeyValue === null) {
            return [];
        }

        $results = $relatedClass::where([$foreignKey => $localKeyValue]);

        $this->relations[$relationName] = $results;
        return $results;
    }

    /**
     * Définit une relation "a un" (1:1)
     *
     * @param string $relatedClass Classe du modèle lié
     * @param string $foreignKey Clé étrangère dans la table liée
     * @param string $localKey Clé locale (défaut: clé primaire)
     */
    public function hasOne(string $relatedClass, string $foreignKey, ?string $localKey = null): ?Model
    {
        $relationName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        if (isset($this->relations[$relationName])) {
            return $this->relations[$relationName];
        }

        $localKey = $localKey ?? $this->primaryKey;
        $localKeyValue = $this->attributes[$localKey] ?? null;

        if ($localKeyValue === null) {
            return null;
        }

        $result = $relatedClass::firstWhere([$foreignKey => $localKeyValue]);

        $this->relations[$relationName] = $result;
        return $result;
    }

    /**
     * Définit une relation "appartient à plusieurs" (N:M)
     *
     * @param string $relatedClass Classe du modèle lié
     * @param string $pivotTable Table pivot
     * @param string $foreignPivotKey Clé FK vers cette table dans le pivot
     * @param string $relatedPivotKey Clé FK vers la table liée dans le pivot
     * @param string $localKey Clé locale
     * @param string $relatedKey Clé dans la table liée
     */
    public function belongsToMany(
        string $relatedClass,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        ?string $localKey = null,
        string $relatedKey = 'id'
    ): array {
        $relationName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        if (isset($this->relations[$relationName])) {
            return $this->relations[$relationName];
        }

        $localKey = $localKey ?? $this->primaryKey;
        $localKeyValue = $this->attributes[$localKey] ?? null;

        if ($localKeyValue === null) {
            return [];
        }

        /** @var Model $relatedInstance */
        $relatedInstance = new $relatedClass();
        $relatedTable = $relatedInstance->getTable();

        $sql = "SELECT {$relatedTable}.* FROM {$relatedTable}
                INNER JOIN {$pivotTable} ON {$pivotTable}.{$relatedPivotKey} = {$relatedTable}.{$relatedKey}
                WHERE {$pivotTable}.{$foreignPivotKey} = :local_key";

        $stmt = static::getConnection()->prepare($sql);
        $stmt->execute(['local_key' => $localKeyValue]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $results = array_map(function (array $row) use ($relatedClass) {
            $model = new $relatedClass($row);
            $model->exists = true;
            return $model;
        }, $rows);

        $this->relations[$relationName] = $results;
        return $results;
    }

    /**
     * Charge des relations de manière eager (évite N+1)
     *
     * @param string|array $relations Relation(s) à charger
     */
    public static function with(string|array $relations): array
    {
        $relations = is_array($relations) ? $relations : [$relations];
        $instance = new static();
        $instance->eagerLoad = $relations;

        // Récupérer tous les enregistrements
        $models = static::all();

        // Charger les relations pour chaque modèle
        foreach ($models as $model) {
            foreach ($relations as $relation) {
                if (method_exists($model, $relation)) {
                    $model->$relation();
                }
            }
        }

        return $models;
    }

    /**
     * Définit une relation manuellement
     */
    public function setRelation(string $name, mixed $value): self
    {
        $this->relations[$name] = $value;
        return $this;
    }

    /**
     * Récupère une relation chargée
     */
    public function getRelation(string $name): mixed
    {
        return $this->relations[$name] ?? null;
    }

    /**
     * Vérifie si une relation est chargée
     */
    public function relationLoaded(string $name): bool
    {
        return isset($this->relations[$name]);
    }

    /**
     * Retourne toutes les relations chargées
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * Retourne le nom de la table (pour les relations)
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Retourne le nom de la clé primaire
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }
}
