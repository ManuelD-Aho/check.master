<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Brouillon
 * 
 * Gestion des brouillons de saisie (ex: formulaires longs).
 * Table: brouillons
 */
class Brouillon extends Model
{
    protected string $table = 'brouillons';
    protected string $primaryKey = 'id_brouillon';
    protected array $fillable = [
        'utilisateur_id',
        'type_formulaire', // ex: 'candidature', 'compte_rendu'
        'donnees_json',
        'cree_le',
        'mis_a_jour_le',
    ];

    /**
     * Sauvegarde un brouillon
     */
    public static function sauvegarder(int $userId, string $type, array $data): self
    {
        $existing = self::firstWhere([
            'utilisateur_id' => $userId,
            'type_formulaire' => $type,
        ]);

        if ($existing) {
            $existing->donnees_json = json_encode($data);
            $existing->mis_a_jour_le = date('Y-m-d H:i:s');
            $existing->save();
            return $existing;
        }

        $brouillon = new self([
            'utilisateur_id' => $userId,
            'type_formulaire' => $type,
            'donnees_json' => json_encode($data),
            'cree_le' => date('Y-m-d H:i:s'),
            'mis_a_jour_le' => date('Y-m-d H:i:s'),
        ]);
        $brouillon->save();
        return $brouillon;
    }

    /**
     * Récupère un brouillon
     */
    public static function recuperer(int $userId, string $type): ?array
    {
        $brouillon = self::firstWhere([
            'utilisateur_id' => $userId,
            'type_formulaire' => $type,
        ]);

        return $brouillon ? json_decode($brouillon->donnees_json, true) : null;
    }

    /**
     * Supprime un brouillon
     */
    public static function supprimer(int $userId, string $type): void
    {
        $brouillon = self::firstWhere([
            'utilisateur_id' => $userId,
            'type_formulaire' => $type,
        ]);

        if ($brouillon) {
            $brouillon->delete();
        }
    }
}
