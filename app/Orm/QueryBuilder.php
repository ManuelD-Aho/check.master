<?php

declare(strict_types=1);

namespace App\Orm;

use PDO;

/**
 * Fluent Query Builder pour CheckMaster
 * 
 * Fournit une API fluide pour construire des requêtes SQL.
 * Toutes les requêtes utilisent des prepared statements.
 */
class QueryBuilder
{
    private string $table = '';
    private array $select = ['*'];
    private array $where = [];
    private array $bindings = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private array $having = [];
    private array $joins = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private bool $distinct = false;

    /**
     * Crée un nouveau QueryBuilder pour une table
     */
    public static function table(string $table): self
    {
        $builder = new self();
        $builder->table = $table;
        return $builder;
    }

    /**
     * Définit les colonnes à sélectionner
     */
    public function select(string|array $columns = ['*']): self
    {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Ajoute DISTINCT à la requête
     */
    public function distinct(): self
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Ajoute une condition WHERE
     */
    public function where(string $column, mixed $operatorOrValue = null, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operatorOrValue;
            $operator = '=';
        } else {
            $operator = $operatorOrValue;
        }

        $placeholder = $this->generatePlaceholder($column);
        $this->where[] = [
            'type' => 'AND',
            'clause' => "{$column} {$operator} :{$placeholder}",
        ];
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    /**
     * Ajoute une condition OR WHERE
     */
    public function orWhere(string $column, mixed $operatorOrValue = null, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operatorOrValue;
            $operator = '=';
        } else {
            $operator = $operatorOrValue;
        }

        $placeholder = $this->generatePlaceholder($column);
        $this->where[] = [
            'type' => 'OR',
            'clause' => "{$column} {$operator} :{$placeholder}",
        ];
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    /**
     * Ajoute une condition WHERE IN
     */
    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            $this->where[] = [
                'type' => 'AND',
                'clause' => '1 = 0', // Always false
            ];
            return $this;
        }

        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = $this->generatePlaceholder($column . '_in_' . $i);
            $placeholders[] = ':' . $placeholder;
            $this->bindings[$placeholder] = $value;
        }

        $this->where[] = [
            'type' => 'AND',
            'clause' => "{$column} IN (" . implode(', ', $placeholders) . ')',
        ];

        return $this;
    }

    /**
     * Ajoute une condition WHERE NOT IN
     */
    public function whereNotIn(string $column, array $values): self
    {
        if (empty($values)) {
            return $this;
        }

        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = $this->generatePlaceholder($column . '_notin_' . $i);
            $placeholders[] = ':' . $placeholder;
            $this->bindings[$placeholder] = $value;
        }

        $this->where[] = [
            'type' => 'AND',
            'clause' => "{$column} NOT IN (" . implode(', ', $placeholders) . ')',
        ];

        return $this;
    }

    /**
     * Ajoute une condition WHERE NULL
     */
    public function whereNull(string $column): self
    {
        $this->where[] = [
            'type' => 'AND',
            'clause' => "{$column} IS NULL",
        ];
        return $this;
    }

    /**
     * Ajoute une condition WHERE NOT NULL
     */
    public function whereNotNull(string $column): self
    {
        $this->where[] = [
            'type' => 'AND',
            'clause' => "{$column} IS NOT NULL",
        ];
        return $this;
    }

    /**
     * Ajoute une condition WHERE BETWEEN
     */
    public function whereBetween(string $column, mixed $min, mixed $max): self
    {
        $minPlaceholder = $this->generatePlaceholder($column . '_min');
        $maxPlaceholder = $this->generatePlaceholder($column . '_max');

        $this->where[] = [
            'type' => 'AND',
            'clause' => "{$column} BETWEEN :{$minPlaceholder} AND :{$maxPlaceholder}",
        ];
        $this->bindings[$minPlaceholder] = $min;
        $this->bindings[$maxPlaceholder] = $max;

        return $this;
    }

    /**
     * Ajoute une condition WHERE LIKE
     */
    public function whereLike(string $column, string $pattern): self
    {
        $placeholder = $this->generatePlaceholder($column . '_like');
        $this->where[] = [
            'type' => 'AND',
            'clause' => "{$column} LIKE :{$placeholder}",
        ];
        $this->bindings[$placeholder] = $pattern;

        return $this;
    }

    /**
     * Ajoute un INNER JOIN
     */
    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    /**
     * Ajoute un LEFT JOIN
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    /**
     * Ajoute un RIGHT JOIN
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "RIGHT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    /**
     * Ajoute un ORDER BY
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    /**
     * Ajoute un GROUP BY
     */
    public function groupBy(string|array $columns): self
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    /**
     * Ajoute une clause HAVING
     */
    public function having(string $column, string $operator, mixed $value): self
    {
        $placeholder = $this->generatePlaceholder('having_' . $column);
        $this->having[] = "{$column} {$operator} :{$placeholder}";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    /**
     * Définit la limite
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Définit l'offset
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Raccourci pour paginer les résultats
     */
    public function paginate(int $page, int $perPage = 15): self
    {
        $this->limit = $perPage;
        $this->offset = ($page - 1) * $perPage;
        return $this;
    }

    /**
     * Construit la requête SQL SELECT
     */
    public function toSql(): string
    {
        $sql = 'SELECT ';

        if ($this->distinct) {
            $sql .= 'DISTINCT ';
        }

        $sql .= implode(', ', $this->select);
        $sql .= " FROM {$this->table}";

        // Joins
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        // Where
        if (!empty($this->where)) {
            $sql .= ' WHERE ';
            $clauses = [];
            foreach ($this->where as $i => $condition) {
                if ($i === 0) {
                    $clauses[] = $condition['clause'];
                } else {
                    $clauses[] = $condition['type'] . ' ' . $condition['clause'];
                }
            }
            $sql .= implode(' ', $clauses);
        }

        // Group By
        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        // Having
        if (!empty($this->having)) {
            $sql .= ' HAVING ' . implode(' AND ', $this->having);
        }

        // Order By
        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        // Limit & Offset
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    /**
     * Retourne les bindings
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Exécute la requête et retourne les résultats
     */
    public function get(PDO $pdo): array
    {
        $stmt = $pdo->prepare($this->toSql());
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Exécute la requête et retourne le premier résultat
     */
    public function first(PDO $pdo): ?array
    {
        $this->limit(1);
        $results = $this->get($pdo);
        return $results[0] ?? null;
    }

    /**
     * Compte le nombre de résultats
     */
    public function count(PDO $pdo): int
    {
        $originalSelect = $this->select;
        $this->select = ['COUNT(*) as count'];

        $result = $this->first($pdo);

        $this->select = $originalSelect;

        return (int) ($result['count'] ?? 0);
    }

    /**
     * Vérifie si des résultats existent
     */
    public function exists(PDO $pdo): bool
    {
        return $this->count($pdo) > 0;
    }

    /**
     * Génère un placeholder unique
     */
    private function generatePlaceholder(string $base): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9_]/', '_', $base);
        $unique = $clean . '_' . count($this->bindings);
        return $unique;
    }

    /**
     * Réinitialise le builder
     */
    public function reset(): self
    {
        $this->select = ['*'];
        $this->where = [];
        $this->bindings = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->joins = [];
        $this->limit = null;
        $this->offset = null;
        $this->distinct = false;
        return $this;
    }
}
