<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Approuver
 * 
 * Enregistre les approbations manuelles (ex: PV, Documents).
 * Table: approuver
 */
class Approuver extends Model
{
    protected string $table = 'approuver';
    protected string $primaryKey = 'id_approbation';
    protected array $fillable = [
        'utilisateur_id',
        'objet_type', // ex: 'soutenance', 'document'
        'objet_id',
        'date_approbation',
        'commentaire',
        'statut', // 'Approuve', 'Rejete'
    ];

    public const STATUT_APPROUVE = 'Approuve';
    public const STATUT_REJETE = 'Rejete';

    /**
     * Retourne l'utilisateur ayant approuvé
     */
    public function getUtilisateur(): ?Utilisateur
    {
        if ($this->utilisateur_id === null) {
            return null;
        }
        return Utilisateur::find((int) $this->utilisateur_id);
    }

    /**
     * Déjà approuvé par cet utilisateur ?
     */
    public static function aApprouve(int $userId, string $type, int $objetId): bool
    {
        return self::firstWhere([
            'utilisateur_id' => $userId,
            'objet_type' => $type,
            'objet_id' => $objetId,
            'statut' => self::STATUT_APPROUVE
        ]) !== null;
    }
}
