<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Penalite
 * 
 * Représente une pénalité appliquée à un étudiant.
 * Table: penalites
 */
class Penalite extends Model
{
    protected string $table = 'penalites';
    protected string $primaryKey = 'id_penalite';
    protected array $fillable = [
        'etudiant_id',
        'montant',
        'motif',
        'date_application',
        'payee',
        'date_paiement',
        'recu_chemin',
    ];

    /**
     * Types de pénalités
     */
    public const TYPE_RETARD = 'Retard';
    public const TYPE_ABSENCE = 'Absence';
    public const TYPE_DOCUMENT = 'Document_manquant';
    public const TYPE_AUTRE = 'Autre';

    /**
     * Retourne l'étudiant
     */
    public function getEtudiant(): ?Etudiant
    {
        if ($this->etudiant_id === null) {
            return null;
        }
        return Etudiant::find((int) $this->etudiant_id);
    }

    /**
     * Marque la pénalité comme payée
     */
    public function marquerPayee(): void
    {
        $this->payee = true;
        $this->date_paiement = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Applique une pénalité
     */
    public static function appliquer(
        int $etudiantId,
        float $montant,
        string $motif
    ): self {
        $penalite = new self([
            'etudiant_id' => $etudiantId,
            'montant' => $montant,
            'motif' => $motif,
            'date_application' => date('Y-m-d'),
            'payee' => false,
        ]);
        $penalite->save();
        return $penalite;
    }

    /**
     * Retourne les pénalités impayées d'un étudiant
     *
     * @return self[]
     */
    public static function impayees(int $etudiantId): array
    {
        return self::where([
            'etudiant_id' => $etudiantId,
            'payee' => false,
        ]);
    }

    /**
     * Calcule le total des pénalités impayées
     */
    public static function totalImpaye(int $etudiantId): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) FROM penalites 
                WHERE etudiant_id = :id AND payee = 0";

        $stmt = self::raw($sql, ['id' => $etudiantId]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Vérifie si l'étudiant a des pénalités bloquantes
     */
    public static function aDesBloquantes(int $etudiantId): bool
    {
        return self::totalImpaye($etudiantId) > 0;
    }
}
