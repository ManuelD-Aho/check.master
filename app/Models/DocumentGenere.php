<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle DocumentGenere
 * 
 * Historique des documents générés par le système (PDFs).
 * Table: documents_generes
 */
class DocumentGenere extends Model
{
    protected string $table = 'documents_generes';
    protected string $primaryKey = 'id_document';
    protected array $fillable = [
        'type_document',
        'entite_type',
        'entite_id',
        'chemin_fichier',
        'nom_fichier',
        'taille_octets',
        'hash_sha256',
        'genere_par',
        'genere_le',
    ];

    /**
     * Types de documents
     */
    public const TYPE_RECU_PAIEMENT = 'recu_paiement';
    public const TYPE_RECU_PENALITE = 'recu_penalite';
    public const TYPE_BULLETIN_NOTES = 'bulletin_notes';
    public const TYPE_PV_COMMISSION = 'pv_commission';
    public const TYPE_PV_SOUTENANCE = 'pv_soutenance';
    public const TYPE_CONVOCATION = 'convocation';
    public const TYPE_ATTESTATION_DIPLOME = 'attestation_diplome';
    public const TYPE_RAPPORT_EVALUATION = 'rapport_evaluation';
    public const TYPE_BULLETIN_PROVISOIRE = 'bulletin_provisoire';
    public const TYPE_CERTIFICAT_SCOLARITE = 'certificat_scolarite';
    public const TYPE_LETTRE_JURY = 'lettre_jury';
    public const TYPE_ATTESTATION_STAGE = 'attestation_stage';
    public const TYPE_BORDEREAU_TRANSMISSION = 'bordereau_transmission';

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur qui a généré le document
     */
    public function createur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'genere_par', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les documents d'une entité
     * @return self[]
     */
    public static function pourEntite(string $type, int $id): array
    {
        $sql = "SELECT * FROM documents_generes 
                WHERE entite_type = :type AND entite_id = :id 
                ORDER BY genere_le DESC";

        $stmt = self::raw($sql, ['type' => $type, 'id' => $id]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les documents par type
     * @return self[]
     */
    public static function parType(string $typeDocument, int $limit = 50): array
    {
        $sql = "SELECT * FROM documents_generes 
                WHERE type_document = :type 
                ORDER BY genere_le DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('type', $typeDocument, \PDO::PARAM_STR);
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
     * Retourne les documents générés par un utilisateur
     * @return self[]
     */
    public static function parUtilisateur(int $utilisateurId, int $limit = 50): array
    {
        $sql = "SELECT * FROM documents_generes 
                WHERE genere_par = :id 
                ORDER BY genere_le DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('id', $utilisateurId, \PDO::PARAM_INT);
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
     * Enregistre un nouveau document généré
     */
    public static function enregistrer(
        string $typeDocument,
        string $cheminFichier,
        string $nomFichier,
        ?int $generePar = null,
        ?string $entiteType = null,
        ?int $entiteId = null
    ): self {
        $document = new self([
            'type_document' => $typeDocument,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'chemin_fichier' => $cheminFichier,
            'nom_fichier' => $nomFichier,
            'taille_octets' => file_exists($cheminFichier) ? filesize($cheminFichier) : 0,
            'hash_sha256' => file_exists($cheminFichier) ? hash_file('sha256', $cheminFichier) : '',
            'genere_par' => $generePar,
            'genere_le' => date('Y-m-d H:i:s'),
        ]);
        $document->save();
        return $document;
    }

    /**
     * Vérifie l'intégrité du fichier
     */
    public function verifierIntegrite(): bool
    {
        if (!file_exists($this->chemin_fichier)) {
            return false;
        }
        $hashActuel = hash_file('sha256', $this->chemin_fichier);
        return $hashActuel === $this->hash_sha256;
    }

    /**
     * Retourne le chemin complet du fichier
     */
    public function getCheminComplet(): string
    {
        return $this->chemin_fichier ?? '';
    }

    /**
     * Vérifie si le fichier existe
     */
    public function fichierExiste(): bool
    {
        return file_exists($this->chemin_fichier ?? '');
    }

    /**
     * Retourne la taille formatée
     */
    public function getTailleFormatee(): string
    {
        $taille = (int) $this->taille_octets;
        if ($taille < 1024) {
            return $taille . ' octets';
        }
        if ($taille < 1048576) {
            return round($taille / 1024, 2) . ' Ko';
        }
        return round($taille / 1048576, 2) . ' Mo';
    }

    /**
     * Statistiques par type de document
     */
    public static function statistiquesParType(): array
    {
        $sql = "SELECT type_document, COUNT(*) as total, 
                SUM(taille_octets) as taille_totale
                FROM documents_generes
                GROUP BY type_document
                ORDER BY total DESC";

        $stmt = self::raw($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
