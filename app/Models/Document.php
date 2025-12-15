<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Document
 * 
 * Représente un document uploadé dans le système.
 * Table: documents
 */
class Document extends Model
{
    protected string $table = 'documents';
    protected string $primaryKey = 'id_document';
    protected array $fillable = [
        'type_document',
        'entite_type',
        'entite_id',
        'nom_fichier',
        'nom_original',
        'chemin',
        'mime_type',
        'taille',
        'hash_sha256',
        'uploade_par',
    ];

    /**
     * Types de documents
     */
    public const TYPE_RAPPORT = 'rapport';
    public const TYPE_ATTESTATION = 'attestation';
    public const TYPE_PV = 'pv';
    public const TYPE_RECU = 'recu';
    public const TYPE_AUTRE = 'autre';

    /**
     * Taille max (5 MB)
     */
    public const TAILLE_MAX = 5242880;

    /**
     * Extensions autorisées
     */
    public const EXTENSIONS_AUTORISEES = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    /**
     * Retourne l'URL de téléchargement
     */
    public function getUrl(): string
    {
        return '/documents/download/' . $this->getId();
    }

    /**
     * Vérifie l'intégrité du fichier
     */
    public function verifierIntegrite(): bool
    {
        if (!file_exists($this->chemin)) {
            return false;
        }

        $hash = hash_file('sha256', $this->chemin);
        return $hash === $this->hash_sha256;
    }

    /**
     * Retourne la taille formatée
     */
    public function getTailleFormatee(): string
    {
        $bytes = (int) $this->taille;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Enregistre un nouveau document
     */
    public static function enregistrer(
        string $type,
        string $cheminTemp,
        string $nomOriginal,
        string $entiteType,
        int $entiteId,
        int $uploadeParId
    ): self {
        // Générer un nom unique
        $extension = pathinfo($nomOriginal, PATHINFO_EXTENSION);
        $nomFichier = uniqid('doc_') . '.' . $extension;

        // Chemin de destination
        $dossier = dirname(__DIR__, 2) . '/storage/documents/' . date('Y/m');
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }
        $chemin = $dossier . '/' . $nomFichier;

        // Déplacer le fichier
        move_uploaded_file($cheminTemp, $chemin);

        // Créer l'entrée
        $document = new self([
            'type_document' => $type,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'nom_fichier' => $nomFichier,
            'nom_original' => $nomOriginal,
            'chemin' => $chemin,
            'mime_type' => mime_content_type($chemin),
            'taille' => filesize($chemin),
            'hash_sha256' => hash_file('sha256', $chemin),
            'uploade_par' => $uploadeParId,
        ]);
        $document->save();

        return $document;
    }

    /**
     * Retourne les documents d'une entité
     *
     * @return self[]
     */
    public static function pourEntite(string $type, int $id): array
    {
        return self::where([
            'entite_type' => $type,
            'entite_id' => $id,
        ]);
    }

    /**
     * Supprime le document (fichier + entrée)
     */
    public function delete(): bool
    {
        // Supprimer le fichier physique
        if (file_exists($this->chemin)) {
            unlink($this->chemin);
        }

        return parent::delete();
    }
}
