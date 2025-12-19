<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Archive
 * 
 * Gestion des archives documentaires avec intégrité.
 * Table: archives
 */
class Archive extends Model
{
    protected string $table = 'archives';
    protected string $primaryKey = 'id_archive';
    protected array $fillable = [
        'document_id',
        'hash_sha256',
        'verifie',
        'derniere_verification',
        'verrouille',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne le document associé
     */
    public function document(): ?DocumentGenere
    {
        return $this->belongsTo(DocumentGenere::class, 'document_id', 'id_document');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les archives non vérifiées
     * @return self[]
     */
    public static function nonVerifiees(): array
    {
        return self::where(['verifie' => false]);
    }

    /**
     * Retourne les archives verrouillées
     * @return self[]
     */
    public static function verrouillees(): array
    {
        return self::where(['verrouille' => true]);
    }

    /**
     * Retourne les archives nécessitant une vérification
     * @return self[]
     */
    public static function aVerifier(int $joursDepuisDerniereVerification = 30): array
    {
        $sql = "SELECT * FROM archives 
                WHERE derniere_verification < DATE_SUB(NOW(), INTERVAL :jours DAY)
                ORDER BY derniere_verification ASC";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('jours', $joursDepuisDerniereVerification, \PDO::PARAM_INT);
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
     * Crée une archive à partir d'un document généré
     */
    public static function creerDepuisDocument(int $documentId): ?self
    {
        $document = DocumentGenere::find($documentId);
        if ($document === null) {
            return null;
        }

        $archive = new self([
            'document_id' => $documentId,
            'hash_sha256' => $document->hash_sha256,
            'verifie' => true,
            'derniere_verification' => date('Y-m-d H:i:s'),
            'verrouille' => true,
        ]);
        $archive->save();
        return $archive;
    }

    /**
     * Vérifie l'intégrité de l'archive
     */
    public function verifierIntegrite(): bool
    {
        $document = $this->document();
        if ($document === null) {
            return false;
        }

        $integre = $document->verifierIntegrite() && $document->hash_sha256 === $this->hash_sha256;

        // Mettre à jour le statut de vérification
        $this->verifie = $integre;
        $this->derniere_verification = date('Y-m-d H:i:s');
        $this->save();

        return $integre;
    }

    /**
     * Verrouille l'archive
     */
    public function verrouiller(): void
    {
        $this->verrouille = true;
        $this->save();
    }

    /**
     * Déverrouille l'archive (nécessite des droits spéciaux)
     */
    public function deverrouiller(): void
    {
        $this->verrouille = false;
        $this->save();
    }

    /**
     * Vérifie si l'archive est intègre
     */
    public function estIntegre(): bool
    {
        return (bool) $this->verifie;
    }

    /**
     * Vérifie si l'archive est verrouillée
     */
    public function estVerrouillee(): bool
    {
        return (bool) $this->verrouille;
    }

    /**
     * Exécute une vérification en masse des archives
     */
    public static function verifierToutesArchives(): array
    {
        $archives = self::all();
        $resultats = [
            'total' => count($archives),
            'integres' => 0,
            'corrompues' => 0,
            'erreurs' => [],
        ];

        foreach ($archives as $archive) {
            if ($archive->verifierIntegrite()) {
                $resultats['integres']++;
            } else {
                $resultats['corrompues']++;
                $resultats['erreurs'][] = $archive->getId();
            }
        }

        return $resultats;
    }

    /**
     * Statistiques des archives
     */
    public static function statistiques(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN verifie = 1 THEN 1 ELSE 0 END) as verifiees,
                    SUM(CASE WHEN verrouille = 1 THEN 1 ELSE 0 END) as verrouillees
                FROM archives";

        $stmt = self::raw($sql);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
