<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle ImportHistorique
 * 
 * Historique des imports de données (CSV/Excel).
 * Table: imports_historiques
 */
class ImportHistorique extends Model
{
    protected string $table = 'imports_historiques';
    protected string $primaryKey = 'id_import';
    protected array $fillable = [
        'type_import',
        'fichier_nom',
        'nb_lignes_totales',
        'nb_lignes_reussies',
        'nb_lignes_erreurs',
        'erreurs_json',
        'importe_par',
    ];

    /**
     * Types d'import
     */
    public const TYPE_ETUDIANTS = 'etudiants';
    public const TYPE_ENSEIGNANTS = 'enseignants';
    public const TYPE_NOTES = 'notes';
    public const TYPE_PAIEMENTS = 'paiements';

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur ayant importé
     */
    public function importateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'importe_par', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les imports récents
     * @return self[]
     */
    public static function recents(int $limit = 50): array
    {
        $sql = "SELECT * FROM imports_historiques 
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les imports par type
     * @return self[]
     */
    public static function parType(string $type, int $limit = 20): array
    {
        $sql = "SELECT * FROM imports_historiques 
                WHERE type_import = :type
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('type', $type, \PDO::PARAM_STR);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Enregistre un nouvel import
     */
    public static function enregistrer(
        string $type,
        string $fichierNom,
        int $nbLignesTotales,
        int $nbLignesReussies,
        int $nbLignesErreurs,
        array $erreurs = [],
        ?int $importePar = null
    ): self {
        $import = new self([
            'type_import' => $type,
            'fichier_nom' => $fichierNom,
            'nb_lignes_totales' => $nbLignesTotales,
            'nb_lignes_reussies' => $nbLignesReussies,
            'nb_lignes_erreurs' => $nbLignesErreurs,
            'erreurs_json' => json_encode($erreurs),
            'importe_par' => $importePar,
        ]);
        $import->save();
        return $import;
    }

    /**
     * Retourne les erreurs décodées
     */
    public function getErreurs(): array
    {
        if (empty($this->erreurs_json)) {
            return [];
        }
        return json_decode($this->erreurs_json, true) ?? [];
    }

    /**
     * Calcule le taux de succès
     */
    public function tauxSucces(): float
    {
        $total = (int) $this->nb_lignes_totales;
        if ($total === 0) {
            return 0.0;
        }
        return round(((int) $this->nb_lignes_reussies / $total) * 100, 2);
    }

    /**
     * Vérifie si l'import a des erreurs
     */
    public function aDesErreurs(): bool
    {
        return (int) $this->nb_lignes_erreurs > 0;
    }
}
