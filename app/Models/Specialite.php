<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Specialite
 * 
 * Représente une spécialité académique.
 * Table: specialites
 */
class Specialite extends Model
{
    protected string $table = 'specialites';
    protected string $primaryKey = 'id_specialite';
    protected array $fillable = [
        'lib_specialite',
        'description',
        'actif',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne les enseignants de cette spécialité
     * @return Enseignant[]
     */
    public function enseignants(): array
    {
        return $this->hasMany(Enseignant::class, 'specialite_id', 'id_specialite');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_specialite' => $libelle]);
    }

    /**
     * Retourne toutes les spécialités actives
     * @return self[]
     */
    public static function actives(): array
    {
        return self::where(['actif' => true]);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si la spécialité est active
     */
    public function estActive(): bool
    {
        return (bool) $this->actif;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Compte les enseignants de cette spécialité
     */
    public function nombreEnseignants(): int
    {
        return Enseignant::count(['specialite_id' => $this->getId(), 'actif' => true]);
    }

    /**
     * Active la spécialité
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive la spécialité
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }
}
