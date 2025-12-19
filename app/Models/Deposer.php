<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Deposer
 * 
 * Enregistre les actions de dépôt de documents par les étudiants.
 * Trace le lien entre un étudiant et un document déposé.
 * Table: deposer
 */
class Deposer extends Model
{
    protected string $table = 'deposer';
    protected string $primaryKey = 'id_depot';
    protected array $fillable = [
        'etudiant_id',
        'document_id',
        'type_document',
        'date_depot',
        'version',
        'commentaire',
        'statut',
    ];

    /**
     * Statuts de dépôt possibles
     */
    public const STATUT_EN_ATTENTE = 'En_attente';
    public const STATUT_VALIDE = 'Valide';
    public const STATUT_REJETE = 'Rejete';

    // ===== RELATIONS =====

    /**
     * Retourne l'étudiant
     */
    public function etudiant(): ?Etudiant
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id', 'id_etudiant');
    }

    /**
     * Retourne le document
     */
    public function document(): ?DocumentGenere
    {
        return $this->belongsTo(DocumentGenere::class, 'document_id', 'id_document');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les dépôts d'un étudiant
     * @return self[]
     */
    public static function pourEtudiant(int $etudiantId): array
    {
        $sql = "SELECT * FROM deposer 
                WHERE etudiant_id = :id 
                ORDER BY date_depot DESC";
        $stmt = self::raw($sql, ['id' => $etudiantId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les dépôts par type de document
     * @return self[]
     */
    public static function parType(int $etudiantId, string $type): array
    {
        return self::where([
            'etudiant_id' => $etudiantId,
            'type_document' => $type,
        ]);
    }

    /**
     * Retourne le dernier dépôt d'un type pour un étudiant
     */
    public static function dernierDepot(int $etudiantId, string $type): ?self
    {
        $sql = "SELECT * FROM deposer 
                WHERE etudiant_id = :etudiant_id AND type_document = :type
                ORDER BY version DESC, date_depot DESC 
                LIMIT 1";

        $stmt = self::raw($sql, [
            'etudiant_id' => $etudiantId,
            'type' => $type,
        ]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne les dépôts en attente de validation
     * @return self[]
     */
    public static function enAttente(): array
    {
        $sql = "SELECT * FROM deposer 
                WHERE statut = :statut 
                ORDER BY date_depot ASC";
        $stmt = self::raw($sql, ['statut' => self::STATUT_EN_ATTENTE]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Enregistre un nouveau dépôt
     */
    public static function enregistrer(
        int $etudiantId,
        int $documentId,
        string $typeDocument,
        ?string $commentaire = null
    ): self {
        // Déterminer la version
        $dernierDepot = self::dernierDepot($etudiantId, $typeDocument);
        $version = $dernierDepot !== null ? ((int) $dernierDepot->version + 1) : 1;

        $depot = new self([
            'etudiant_id' => $etudiantId,
            'document_id' => $documentId,
            'type_document' => $typeDocument,
            'date_depot' => date('Y-m-d H:i:s'),
            'version' => $version,
            'commentaire' => $commentaire,
            'statut' => self::STATUT_EN_ATTENTE,
        ]);
        $depot->save();
        return $depot;
    }

    /**
     * Valide le dépôt
     */
    public function valider(): void
    {
        $this->statut = self::STATUT_VALIDE;
        $this->save();
    }

    /**
     * Rejette le dépôt
     */
    public function rejeter(string $motif): void
    {
        $this->statut = self::STATUT_REJETE;
        $this->commentaire = $motif;
        $this->save();
    }

    /**
     * Vérifie si le dépôt est validé
     */
    public function estValide(): bool
    {
        return $this->statut === self::STATUT_VALIDE;
    }

    /**
     * Vérifie si le dépôt est en attente
     */
    public function estEnAttente(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    /**
     * Vérifie si le dépôt est rejeté
     */
    public function estRejete(): bool
    {
        return $this->statut === self::STATUT_REJETE;
    }

    /**
     * Compte le nombre de dépôts d'un étudiant
     */
    public static function nombreDepots(int $etudiantId): int
    {
        $sql = "SELECT COUNT(*) FROM deposer WHERE etudiant_id = :id";
        $stmt = self::raw($sql, ['id' => $etudiantId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifie si un étudiant a déposé un type de document
     */
    public static function aDepose(int $etudiantId, string $type): bool
    {
        return self::dernierDepot($etudiantId, $type) !== null;
    }
}
