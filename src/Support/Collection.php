<?php

declare(strict_types=1);

namespace Src\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * Collection - Wrapper pour les tableaux
 * 
 * Fournit une API fluide pour la manipulation de tableaux
 * avec méthodes chainables inspirées de Laravel Collection.
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    protected array $items = [];

    /**
     * @param array $items Éléments initiaux
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Factory statique
     */
    public static function make(array $items = []): self
    {
        return new self($items);
    }

    /**
     * Retourne tous les éléments
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Applique un callback à chaque élément
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->items));
    }

    /**
     * Filtre les éléments
     */
    public function filter(?callable $callback = null): self
    {
        if ($callback === null) {
            return new self(array_filter($this->items));
        }

        return new self(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Réduit la collection à une valeur unique
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Retourne le premier élément
     */
    public function first(?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            return $this->items[array_key_first($this->items)] ?? $default;
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Retourne le dernier élément
     */
    public function last(?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            return $this->items[array_key_last($this->items)] ?? $default;
        }

        return $this->reverse()->first($callback, $default);
    }

    /**
     * Inverse l'ordre des éléments
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->items, true));
    }

    /**
     * Extrait une colonne
     */
    public function pluck(string $key, ?string $indexBy = null): self
    {
        $result = [];

        foreach ($this->items as $item) {
            $value = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);

            if ($indexBy !== null) {
                $index = is_array($item) ? ($item[$indexBy] ?? null) : ($item->$indexBy ?? null);
                $result[$index] = $value;
            } else {
                $result[] = $value;
            }
        }

        return new self($result);
    }

    /**
     * Retourne un élément par clé
     */
    public function get(string|int $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe
     */
    public function has(string|int $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Ajoute un élément
     */
    public function push(mixed $value): self
    {
        $this->items[] = $value;
        return $this;
    }

    /**
     * Définit un élément
     */
    public function put(string|int $key, mixed $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * Supprime un élément
     */
    public function forget(string|int $key): self
    {
        unset($this->items[$key]);
        return $this;
    }

    /**
     * Vérifie si la collection est vide
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Vérifie si la collection n'est pas vide
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Compte les éléments
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Trie les éléments
     */
    public function sort(?callable $callback = null): self
    {
        $items = $this->items;

        if ($callback === null) {
            sort($items);
        } else {
            usort($items, $callback);
        }

        return new self($items);
    }

    /**
     * Trie par une clé
     */
    public function sortBy(string $key, bool $descending = false): self
    {
        $items = $this->items;

        usort($items, function ($a, $b) use ($key, $descending) {
            $aVal = is_array($a) ? ($a[$key] ?? null) : ($a->$key ?? null);
            $bVal = is_array($b) ? ($b[$key] ?? null) : ($b->$key ?? null);

            $result = $aVal <=> $bVal;
            return $descending ? -$result : $result;
        });

        return new self($items);
    }

    /**
     * Groupe par une clé
     */
    public function groupBy(string $key): self
    {
        $result = [];

        foreach ($this->items as $item) {
            $groupKey = is_array($item) ? ($item[$key] ?? '') : ($item->$key ?? '');
            $result[$groupKey][] = $item;
        }

        return new self($result);
    }

    /**
     * Retourne les clés uniques
     */
    public function unique(?string $key = null): self
    {
        if ($key === null) {
            return new self(array_unique($this->items));
        }

        $seen = [];
        $result = [];

        foreach ($this->items as $item) {
            $value = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);

            if (!in_array($value, $seen, true)) {
                $seen[] = $value;
                $result[] = $item;
            }
        }

        return new self($result);
    }

    /**
     * Prend N éléments
     */
    public function take(int $limit): self
    {
        return new self(array_slice($this->items, 0, $limit));
    }

    /**
     * Saute N éléments
     */
    public function skip(int $count): self
    {
        return new self(array_slice($this->items, $count));
    }

    /**
     * Divise en chunks
     */
    public function chunk(int $size): self
    {
        return new self(array_chunk($this->items, $size));
    }

    /**
     * Fusionne avec un autre tableau
     */
    public function merge(array $items): self
    {
        return new self(array_merge($this->items, $items));
    }

    /**
     * Retourne la somme
     */
    public function sum(?string $key = null): int|float
    {
        if ($key === null) {
            return array_sum($this->items);
        }

        return $this->pluck($key)->sum();
    }

    /**
     * Retourne la moyenne
     */
    public function avg(?string $key = null): int|float
    {
        $count = $this->count();
        return $count > 0 ? $this->sum($key) / $count : 0;
    }

    /**
     * Retourne le maximum
     */
    public function max(?string $key = null): mixed
    {
        if ($key === null) {
            return max($this->items);
        }

        return $this->pluck($key)->max();
    }

    /**
     * Retourne le minimum
     */
    public function min(?string $key = null): mixed
    {
        if ($key === null) {
            return min($this->items);
        }

        return $this->pluck($key)->min();
    }

    /**
     * Vérifie si un élément satisfait une condition
     */
    public function contains(mixed $key, mixed $value = null): bool
    {
        if ($value === null) {
            return in_array($key, $this->items, true);
        }

        foreach ($this->items as $item) {
            $itemValue = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            if ($itemValue === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convertit en JSON
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->items, $options);
    }

    // ========== Interfaces ==========

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->push($value);
        } else {
            $this->put($offset, $value);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->forget($offset);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }
}
