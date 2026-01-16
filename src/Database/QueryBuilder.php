<?php

declare(strict_types=1);

namespace Src\Database;

use PDO;
use PDOStatement;
use Src\Exceptions\DatabaseException;

/**
 * Query Builder avancé pour construction de requêtes SQL complexes
 * 
 * Support de:
 * - SELECT avec colonnes, DISTINCT, calculs
 * - WHERE avec opérateurs multiples et groupes
 * - JOIN (INNER, LEFT, RIGHT, CROSS)
 * - GROUP BY avec HAVING
 * - ORDER BY avec directions multiples
 * - LIMIT et OFFSET pour pagination
 * - Sous-requêtes et unions
 * - Agrégations (COUNT, SUM, AVG, MIN, MAX)
 * 
 * @package Src\Database
 */
class QueryBuilder
{
    private PDO $pdo;
    private string $table = '';
    private array $select = ['*'];
    private bool $distinct = false;
    private array $joins = [];
    private array $wheres = [];
    private array $bindings = [];
    private array $groupBy = [];
    private array $having = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $unions = [];
    private string $type = 'select'; // select, insert, update, delete

    /**
     * Constructeur
     *
     * @param PDO $pdo Connexion PDO
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Définir la table principale
     *
     * @param string $table Nom de la table
     * @return self
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Alias pour table()
     *
     * @param string $table Nom de la table
     * @return self
     */
    public function from(string $table): self
    {
        return $this->table($table);
    }

    /**
     * Définir les colonnes à sélectionner
     *
     * @param array|string ...$columns Colonnes
     * @return self
     */
    public function select(...$columns): self
    {
        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }

        $this->select = array_map(function ($column) {
            return is_string($column) ? $column : '*';
        }, $columns);

        return $this;
    }

    /**
     * Ajouter DISTINCT à la requête
     *
     * @return self
     */
    public function distinct(): self
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Ajouter une clause WHERE
     *
     * @param string|callable $column Colonne ou callback pour groupe
     * @param mixed $operator Opérateur ou valeur si = implicite
     * @param mixed $value Valeur (optionnel si = implicite)
     * @param string $boolean AND ou OR
     * @return self
     */
    public function where($column, $operator = null, $value = null, string $boolean = 'AND'): self
    {
        // Support pour callback (groupes de conditions)
        if (is_callable($column)) {
            return $this->whereNested($column, $boolean);
        }

        // Si 2 arguments, opérateur = implicite
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => strtoupper($operator),
            'value' => $value,
            'boolean' => $boolean
        ];

        if (!is_null($value)) {
            $this->bindings[] = $value;
        }

        return $this;
    }

    /**
     * Ajouter une clause WHERE avec OR
     *
     * @param string|callable $column Colonne ou callback
     * @param mixed $operator Opérateur ou valeur
     * @param mixed $value Valeur optionnelle
     * @return self
     */
    public function orWhere($column, $operator = null, $value = null): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * WHERE avec condition imbriquée (groupes)
     *
     * @param callable $callback Fonction pour construire le groupe
     * @param string $boolean AND ou OR
     * @return self
     */
    public function whereNested(callable $callback, string $boolean = 'AND'): self
    {
        $query = new static($this->pdo);
        $callback($query);

        if (!empty($query->wheres)) {
            $this->wheres[] = [
                'type' => 'nested',
                'query' => $query,
                'boolean' => $boolean
            ];

            // Ajouter les bindings du groupe
            $this->bindings = array_merge($this->bindings, $query->bindings);
        }

        return $this;
    }

    /**
     * WHERE IN
     *
     * @param string $column Colonne
     * @param array $values Valeurs
     * @param string $boolean AND ou OR
     * @param bool $not NOT IN si true
     * @return self
     */
    public function whereIn(string $column, array $values, string $boolean = 'AND', bool $not = false): self
    {
        if (empty($values)) {
            return $this;
        }

        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean,
            'not' => $not
        ];

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * WHERE NOT IN
     *
     * @param string $column Colonne
     * @param array $values Valeurs
     * @param string $boolean AND ou OR
     * @return self
     */
    public function whereNotIn(string $column, array $values, string $boolean = 'AND'): self
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * WHERE BETWEEN
     *
     * @param string $column Colonne
     * @param mixed $min Valeur minimum
     * @param mixed $max Valeur maximum
     * @param string $boolean AND ou OR
     * @param bool $not NOT BETWEEN si true
     * @return self
     */
    public function whereBetween(string $column, $min, $max, string $boolean = 'AND', bool $not = false): self
    {
        $this->wheres[] = [
            'type' => 'between',
            'column' => $column,
            'min' => $min,
            'max' => $max,
            'boolean' => $boolean,
            'not' => $not
        ];

        $this->bindings[] = $min;
        $this->bindings[] = $max;

        return $this;
    }

    /**
     * WHERE NOT BETWEEN
     *
     * @param string $column Colonne
     * @param mixed $min Valeur minimum
     * @param mixed $max Valeur maximum
     * @param string $boolean AND ou OR
     * @return self
     */
    public function whereNotBetween(string $column, $min, $max, string $boolean = 'AND'): self
    {
        return $this->whereBetween($column, $min, $max, $boolean, true);
    }

    /**
     * WHERE NULL
     *
     * @param string $column Colonne
     * @param string $boolean AND ou OR
     * @param bool $not IS NOT NULL si true
     * @return self
     */
    public function whereNull(string $column, string $boolean = 'AND', bool $not = false): self
    {
        $this->wheres[] = [
            'type' => 'null',
            'column' => $column,
            'boolean' => $boolean,
            'not' => $not
        ];

        return $this;
    }

    /**
     * WHERE NOT NULL
     *
     * @param string $column Colonne
     * @param string $boolean AND ou OR
     * @return self
     */
    public function whereNotNull(string $column, string $boolean = 'AND'): self
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * WHERE avec expression SQL brute
     *
     * @param string $sql Expression SQL
     * @param array $bindings Bindings
     * @param string $boolean AND ou OR
     * @return self
     */
    public function whereRaw(string $sql, array $bindings = [], string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'type' => 'raw',
            'sql' => $sql,
            'boolean' => $boolean
        ];

        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    /**
     * Ajouter un JOIN
     *
     * @param string $table Table à joindre
     * @param string $first Première colonne
     * @param string $operator Opérateur
     * @param string $second Deuxième colonne
     * @param string $type Type de JOIN (INNER, LEFT, RIGHT, CROSS)
     * @return self
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => strtoupper($type)
        ];

        return $this;
    }

    /**
     * LEFT JOIN
     *
     * @param string $table Table
     * @param string $first Première colonne
     * @param string $operator Opérateur
     * @param string $second Deuxième colonne
     * @return self
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * RIGHT JOIN
     *
     * @param string $table Table
     * @param string $first Première colonne
     * @param string $operator Opérateur
     * @param string $second Deuxième colonne
     * @return self
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    /**
     * CROSS JOIN
     *
     * @param string $table Table
     * @return self
     */
    public function crossJoin(string $table): self
    {
        $this->joins[] = [
            'table' => $table,
            'type' => 'CROSS'
        ];

        return $this;
    }

    /**
     * GROUP BY
     *
     * @param array|string ...$columns Colonnes
     * @return self
     */
    public function groupBy(...$columns): self
    {
        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }

        $this->groupBy = array_merge($this->groupBy, $columns);

        return $this;
    }

    /**
     * HAVING
     *
     * @param string $column Colonne (peut être agrégation)
     * @param string $operator Opérateur
     * @param mixed $value Valeur
     * @param string $boolean AND ou OR
     * @return self
     */
    public function having(string $column, string $operator, $value, string $boolean = 'AND'): self
    {
        $this->having[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * OR HAVING
     *
     * @param string $column Colonne
     * @param string $operator Opérateur
     * @param mixed $value Valeur
     * @return self
     */
    public function orHaving(string $column, string $operator, $value): self
    {
        return $this->having($column, $operator, $value, 'OR');
    }

    /**
     * ORDER BY
     *
     * @param string $column Colonne
     * @param string $direction ASC ou DESC
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [
            'column' => $column,
            'direction' => strtoupper($direction)
        ];

        return $this;
    }

    /**
     * ORDER BY DESC (raccourci)
     *
     * @param string $column Colonne
     * @return self
     */
    public function orderByDesc(string $column): self
    {
        return $this->orderBy($column, 'DESC');
    }

    /**
     * ORDER BY ASC (raccourci)
     *
     * @param string $column Colonne
     * @return self
     */
    public function orderByAsc(string $column): self
    {
        return $this->orderBy($column, 'ASC');
    }

    /**
     * LIMIT
     *
     * @param int $limit Nombre maximum de résultats
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit > 0 ? $limit : null;
        return $this;
    }

    /**
     * Alias pour limit()
     *
     * @param int $limit Limite
     * @return self
     */
    public function take(int $limit): self
    {
        return $this->limit($limit);
    }

    /**
     * OFFSET
     *
     * @param int $offset Nombre de résultats à sauter
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset >= 0 ? $offset : null;
        return $this;
    }

    /**
     * Alias pour offset()
     *
     * @param int $offset Offset
     * @return self
     */
    public function skip(int $offset): self
    {
        return $this->offset($offset);
    }

    /**
     * Pagination (limite + offset calculé)
     *
     * @param int $page Numéro de page (1-based)
     * @param int $perPage Items par page
     * @return self
     */
    public function forPage(int $page, int $perPage = 15): self
    {
        $page = max(1, $page);
        return $this->offset(($page - 1) * $perPage)->limit($perPage);
    }

    /**
     * Ajouter une UNION
     *
     * @param QueryBuilder|string $query Query Builder ou SQL brut
     * @param bool $all UNION ALL si true
     * @return self
     */
    public function union($query, bool $all = false): self
    {
        $this->unions[] = [
            'query' => $query,
            'all' => $all
        ];

        if ($query instanceof self) {
            $this->bindings = array_merge($this->bindings, $query->getBindings());
        }

        return $this;
    }

    /**
     * UNION ALL
     *
     * @param QueryBuilder|string $query Query Builder ou SQL
     * @return self
     */
    public function unionAll($query): self
    {
        return $this->union($query, true);
    }

    /**
     * COUNT agrégation
     *
     * @param string $column Colonne (défaut: *)
     * @return int
     */
    public function count(string $column = '*'): int
    {
        return (int) $this->aggregate('COUNT', $column);
    }

    /**
     * SUM agrégation
     *
     * @param string $column Colonne
     * @return float
     */
    public function sum(string $column): float
    {
        return (float) $this->aggregate('SUM', $column);
    }

    /**
     * AVG agrégation
     *
     * @param string $column Colonne
     * @return float
     */
    public function avg(string $column): float
    {
        return (float) $this->aggregate('AVG', $column);
    }

    /**
     * MIN agrégation
     *
     * @param string $column Colonne
     * @return mixed
     */
    public function min(string $column)
    {
        return $this->aggregate('MIN', $column);
    }

    /**
     * MAX agrégation
     *
     * @param string $column Colonne
     * @return mixed
     */
    public function max(string $column)
    {
        return $this->aggregate('MAX', $column);
    }

    /**
     * Fonction d'agrégation générique
     *
     * @param string $function Fonction (COUNT, SUM, AVG, MIN, MAX)
     * @param string $column Colonne
     * @return mixed
     */
    protected function aggregate(string $function, string $column)
    {
        $previousSelect = $this->select;

        $this->select = ["{$function}({$column}) as aggregate"];
        $result = $this->first();
        $this->select = $previousSelect;

        return $result ? $result->aggregate : 0;
    }

    /**
     * Exécuter la requête et retourner tous les résultats
     *
     * @return array
     * @throws DatabaseException
     */
    public function get(): array
    {
        $sql = $this->toSql();
        $stmt = $this->execute($sql, $this->bindings);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Récupérer le premier résultat
     *
     * @return object|null
     * @throws DatabaseException
     */
    public function first(): ?object
    {
        $this->limit(1);
        $results = $this->get();

        return $results[0] ?? null;
    }

    /**
     * Récupérer une seule valeur de colonne
     *
     * @param string $column Nom de la colonne
     * @return mixed
     * @throws DatabaseException
     */
    public function value(string $column)
    {
        $result = $this->first();
        return $result ? $result->{$column} : null;
    }

    /**
     * Récupérer un tableau de valeurs d'une colonne
     *
     * @param string $column Colonne
     * @return array
     * @throws DatabaseException
     */
    public function pluck(string $column): array
    {
        $results = $this->get();
        return array_map(fn($row) => $row->{$column}, $results);
    }

    /**
     * Vérifier si des résultats existent
     *
     * @return bool
     * @throws DatabaseException
     */
    public function exists(): bool
    {
        $sql = "SELECT EXISTS(" . $this->toSql() . ") as `exists`";
        $stmt = $this->execute($sql, $this->bindings);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return (bool) ($result->exists ?? false);
    }

    /**
     * Insérer des données
     *
     * @param array $data Données à insérer
     * @return int ID inséré
     * @throws DatabaseException
     */
    public function insert(array $data): int
    {
        if (empty($data)) {
            throw new DatabaseException("Impossible d'insérer des données vides");
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->execute($sql, array_values($data));

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Insérer plusieurs lignes
     *
     * @param array $rows Tableau de lignes
     * @return int Nombre de lignes insérées
     * @throws DatabaseException
     */
    public function insertMultiple(array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        $firstRow = reset($rows);
        $columns = array_keys($firstRow);
        $placeholderRow = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $placeholders = array_fill(0, count($rows), $placeholderRow);

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES %s",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $bindings = [];
        foreach ($rows as $row) {
            $bindings = array_merge($bindings, array_values($row));
        }

        $stmt = $this->execute($sql, $bindings);

        return $stmt->rowCount();
    }

    /**
     * Mettre à jour des données
     *
     * @param array $data Données à mettre à jour
     * @return int Nombre de lignes affectées
     * @throws DatabaseException
     */
    public function update(array $data): int
    {
        if (empty($data)) {
            throw new DatabaseException("Impossible de mettre à jour avec des données vides");
        }

        $sets = [];
        $values = [];

        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $values[] = $value;
        }

        $sql = sprintf(
            "UPDATE %s SET %s",
            $this->table,
            implode(', ', $sets)
        );

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->compileWheres();
            $values = array_merge($values, $this->bindings);
        }

        $stmt = $this->execute($sql, $values);

        return $stmt->rowCount();
    }

    /**
     * Supprimer des données
     *
     * @return int Nombre de lignes supprimées
     * @throws DatabaseException
     */
    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->compileWheres();
        }

        $stmt = $this->execute($sql, $this->bindings);

        return $stmt->rowCount();
    }

    /**
     * Incrémenter une colonne
     *
     * @param string $column Colonne
     * @param int $amount Montant (défaut: 1)
     * @param array $extra Données supplémentaires à mettre à jour
     * @return int Nombre de lignes affectées
     * @throws DatabaseException
     */
    public function increment(string $column, int $amount = 1, array $extra = []): int
    {
        $sql = "UPDATE {$this->table} SET {$column} = {$column} + ?";
        $bindings = [$amount];

        if (!empty($extra)) {
            foreach ($extra as $key => $value) {
                $sql .= ", {$key} = ?";
                $bindings[] = $value;
            }
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->compileWheres();
            $bindings = array_merge($bindings, $this->bindings);
        }

        $stmt = $this->execute($sql, $bindings);
        return $stmt->rowCount();
    }

    /**
     * Décrémenter une colonne
     *
     * @param string $column Colonne
     * @param int $amount Montant (défaut: 1)
     * @param array $extra Données supplémentaires à mettre à jour
     * @return int Nombre de lignes affectées
     * @throws DatabaseException
     */
    public function decrement(string $column, int $amount = 1, array $extra = []): int
    {
        $sql = "UPDATE {$this->table} SET {$column} = {$column} - ?";
        $bindings = [$amount];

        if (!empty($extra)) {
            foreach ($extra as $key => $value) {
                $sql .= ", {$key} = ?";
                $bindings[] = $value;
            }
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->compileWheres();
            $bindings = array_merge($bindings, $this->bindings);
        }

        $stmt = $this->execute($sql, $bindings);
        return $stmt->rowCount();
    }

    /**
     * Créer une expression SQL brute
     *
     * @param string $value Expression SQL
     * @return object
     */
    public function raw(string $value): object
    {
        return (object) ['raw' => $value];
    }

    /**
     * Construire le SQL complet
     *
     * @return string
     */
    public function toSql(): string
    {
        $sql = [];

        // SELECT
        $sql[] = 'SELECT';
        if ($this->distinct) {
            $sql[] = 'DISTINCT';
        }
        $sql[] = implode(', ', $this->select);

        // FROM
        $sql[] = "FROM {$this->table}";

        // JOINS
        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                if ($join['type'] === 'CROSS') {
                    $sql[] = "CROSS JOIN {$join['table']}";
                } else {
                    $sql[] = "{$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
                }
            }
        }

        // WHERE
        if (!empty($this->wheres)) {
            $sql[] = 'WHERE ' . $this->compileWheres();
        }

        // GROUP BY
        if (!empty($this->groupBy)) {
            $sql[] = 'GROUP BY ' . implode(', ', $this->groupBy);
        }

        // HAVING
        if (!empty($this->having)) {
            $sql[] = 'HAVING ' . $this->compileHaving();
        }

        // ORDER BY
        if (!empty($this->orderBy)) {
            $orders = array_map(
                fn($order) => "{$order['column']} {$order['direction']}",
                $this->orderBy
            );
            $sql[] = 'ORDER BY ' . implode(', ', $orders);
        }

        // LIMIT
        if ($this->limit !== null) {
            $sql[] = "LIMIT {$this->limit}";
        }

        // OFFSET
        if ($this->offset !== null) {
            $sql[] = "OFFSET {$this->offset}";
        }

        $query = implode(' ', $sql);

        // UNIONS
        if (!empty($this->unions)) {
            foreach ($this->unions as $union) {
                $type = $union['all'] ? 'UNION ALL' : 'UNION';
                $unionSql = $union['query'] instanceof self
                    ? $union['query']->toSql()
                    : $union['query'];

                $query .= " {$type} {$unionSql}";
            }
        }

        return $query;
    }

    /**
     * Compiler les clauses WHERE
     *
     * @return string
     */
    protected function compileWheres(): string
    {
        $sql = [];

        foreach ($this->wheres as $index => $where) {
            $boolean = $index === 0 ? '' : " {$where['boolean']} ";

            switch ($where['type']) {
                case 'basic':
                    $sql[] = $boolean . "{$where['column']} {$where['operator']} ?";
                    break;

                case 'nested':
                    $nested = $where['query']->compileWheres();
                    $sql[] = $boolean . "({$nested})";
                    break;

                case 'in':
                    $placeholders = implode(', ', array_fill(0, count($where['values']), '?'));
                    $not = $where['not'] ? 'NOT ' : '';
                    $sql[] = $boolean . "{$where['column']} {$not}IN ({$placeholders})";
                    break;

                case 'between':
                    $not = $where['not'] ? 'NOT ' : '';
                    $sql[] = $boolean . "{$where['column']} {$not}BETWEEN ? AND ?";
                    break;

                case 'null':
                    $not = $where['not'] ? 'NOT ' : '';
                    $sql[] = $boolean . "{$where['column']} IS {$not}NULL";
                    break;

                case 'raw':
                    $sql[] = $boolean . $where['sql'];
                    break;
            }
        }

        return implode('', $sql);
    }

    /**
     * Compiler les clauses HAVING
     *
     * @return string
     */
    protected function compileHaving(): string
    {
        $sql = [];

        foreach ($this->having as $index => $having) {
            $boolean = $index === 0 ? '' : " {$having['boolean']} ";
            $sql[] = $boolean . "{$having['column']} {$having['operator']} ?";
        }

        return implode('', $sql);
    }

    /**
     * Exécuter une requête SQL
     *
     * @param string $sql SQL
     * @param array $bindings Bindings
     * @return PDOStatement
     * @throws DatabaseException
     */
    protected function execute(string $sql, array $bindings = []): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindings);
            return $stmt;
        } catch (\PDOException $e) {
            throw new DatabaseException(
                "Erreur d'exécution de requête: " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Obtenir les bindings
     *
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Debug: Obtenir le SQL avec bindings
     *
     * @return string
     */
    public function toSqlWithBindings(): string
    {
        $sql = $this->toSql();
        $bindings = $this->getBindings();

        foreach ($bindings as $binding) {
            $value = is_string($binding) ? "'{$binding}'" : $binding;
            $sql = preg_replace('/\?/', (string) $value, $sql, 1);
        }

        return $sql;
    }

    /**
     * Réinitialiser le builder
     *
     * @return self
     */
    public function reset(): self
    {
        $this->select = ['*'];
        $this->distinct = false;
        $this->joins = [];
        $this->wheres = [];
        $this->bindings = [];
        $this->groupBy = [];
        $this->having = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->unions = [];

        return $this;
    }
}
