<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Paiement
 * 
 * Représente un paiement effectué par un étudiant.
 * Table: paiements
 */
class Paiement extends Model
{
    protected string $table = 'paiements';
    protected string $primaryKey = 'id_paiement';
    protected array $fillable = [
        'etudiant_id',
        'annee_acad_id',
        'montant',
        'mode_paiement',
        'reference',
        'date_paiement',
        'recu_genere',
        'recu_chemin',
        'enregistre_par',
    ];

    /**
     * Modes de paiement
     */
    public const MODE_ESPECES = 'Especes';
    public const MODE_CARTE = 'Carte';
    public const MODE_VIREMENT = 'Virement';
    public const MODE_CHEQUE = 'Cheque';

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
     * Retourne l'année académique
     */
    public function getAnneeAcademique(): ?AnneeAcademique
    {
        if ($this->annee_acad_id === null) {
            return null;
        }
        return AnneeAcademique::find((int) $this->annee_acad_id);
    }

    /**
     * Génère une référence unique
     */
    public static function genererReference(): string
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    }

    /**
     * Enregistre un paiement
     */
    public static function enregistrer(
        int $etudiantId,
        int $anneeAcadId,
        float $montant,
        string $modePaiement,
        ?int $userId = null
    ): self {
        $paiement = new self([
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
            'montant' => $montant,
            'mode_paiement' => $modePaiement,
            'reference' => self::genererReference(),
            'date_paiement' => date('Y-m-d'),
            'recu_genere' => false,
            'enregistre_par' => $userId,
        ]);
        $paiement->save();
        return $paiement;
    }

    /**
     * Marque le reçu comme généré
     */
    public function marquerRecuGenere(string $chemin): void
    {
        $this->recu_genere = true;
        $this->recu_chemin = $chemin;
        $this->save();
    }

    /**
     * Retourne les paiements d'un étudiant
     *
     * @return self[]
     */
    public static function pourEtudiant(int $etudiantId, ?int $anneeAcadId = null): array
    {
        $conditions = ['etudiant_id' => $etudiantId];
        if ($anneeAcadId !== null) {
            $conditions['annee_acad_id'] = $anneeAcadId;
        }
        return self::where($conditions);
    }

    /**
     * Calcule le total payé par un étudiant
     */
    public static function totalPaye(int $etudiantId, ?int $anneeAcadId = null): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE etudiant_id = :id";
        $params = ['id' => $etudiantId];

        if ($anneeAcadId !== null) {
            $sql .= " AND annee_acad_id = :annee";
            $params['annee'] = $anneeAcadId;
        }

        $stmt = self::raw($sql, $params);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Retourne les paiements du jour
     *
     * @return self[]
     */
    public static function duJour(): array
    {
        $sql = "SELECT * FROM paiements WHERE DATE(date_paiement) = :today ORDER BY date_paiement DESC";
        $stmt = self::raw($sql, ['today' => date('Y-m-d')]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Calcule le total encaissé pour une période
     */
    public static function totalPeriode(string $dateDebut, string $dateFin): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) FROM paiements 
                WHERE date_paiement >= :debut AND date_paiement <= :fin";

        $stmt = self::raw($sql, ['debut' => $dateDebut, 'fin' => $dateFin]);
        return (float) $stmt->fetchColumn();
    }
}
