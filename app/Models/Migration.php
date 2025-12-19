<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Migration
 * 
 * Suivi des migrations de base de données exécutées.
 * Table: migrations
 */
class Migration extends Model
{
    protected string $table = 'migrations';
    protected string $primaryKey = 'id_migration';
    protected array $fillable = [
        'migration_name',
        'executed_at',
    ];

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve une migration par son nom
     */
    public static function findByName(string $name): ?self
    {
        return self::firstWhere(['migration_name' => $name]);
    }

    /**
     * Vérifie si une migration a été exécutée
     */
    public static function estExecutee(string $name): bool
    {
        return self::findByName($name) !== null;
    }

    /**
     * Retourne toutes les migrations exécutées
     * @return self[]
     */
    public static function executees(): array
    {
        $sql = "SELECT * FROM migrations ORDER BY executed_at ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne la dernière migration
     */
    public static function derniere(): ?self
    {
        $sql = "SELECT * FROM migrations ORDER BY executed_at DESC LIMIT 1";
        $stmt = self::raw($sql);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Enregistre une migration comme exécutée
     */
    public static function enregistrer(string $name): self
    {
        // Vérifier si elle existe déjà
        $existante = self::findByName($name);
        if ($existante) {
            return $existante;
        }

        $migration = new self([
            'migration_name' => $name,
            'executed_at' => date('Y-m-d H:i:s'),
        ]);
        $migration->save();
        return $migration;
    }

    /**
     * Supprime une migration (rollback)
     */
    public static function annuler(string $name): bool
    {
        $migration = self::findByName($name);
        if ($migration) {
            return $migration->delete();
        }
        return false;
    }

    /**
     * Retourne les migrations manquantes d'une liste
     */
    public static function manquantes(array $toutesLesMigrations): array
    {
        $executees = array_map(fn($m) => $m->migration_name, self::executees());
        return array_diff($toutesLesMigrations, $executees);
    }
}
