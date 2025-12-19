<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Fonction
 * 
 * Représente une fonction administrative ou académique.
 * Table: fonctions
 */
class Fonction extends Model
{
    protected string $table = 'fonctions';
    protected string $primaryKey = 'id_fonction';
    protected array $fillable = [
        'lib_fonction',
        'description',
        'actif',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne les enseignants avec cette fonction
     * @return Enseignant[]
     */
    public function enseignants(): array
    {
        return $this->hasMany(Enseignant::class, 'fonction_id', 'id_fonction');
    }

    /**
     * Retourne le personnel admin avec cette fonction
     * @return PersonnelAdmin[]
     */
    public function personnelAdmin(): array
    {
        return $this->hasMany(PersonnelAdmin::class, 'fonction_id', 'id_fonction');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_fonction' => $libelle]);
    }

    /**
     * Retourne toutes les fonctions actives
     * @return self[]
     */
    public static function actives(): array
    {
        return self::where(['actif' => true]);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si la fonction est active
     */
    public function estActive(): bool
    {
        return (bool) $this->actif;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Active la fonction
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive la fonction
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }
}
