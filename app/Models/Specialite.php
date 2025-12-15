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

    /**
     * Trouve une spécialité par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_specialite' => $libelle]);
    }

    /**
     * Retourne toutes les spécialités actives
     *
     * @return self[]
     */
    public static function actives(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les enseignants de cette spécialité
     */
    public function getEnseignants(): array
    {
        return Enseignant::where(['specialite_id' => $this->getId(), 'actif' => true]);
    }
}
