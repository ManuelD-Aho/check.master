<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Archive
 * 
 * Gestion des archives documentaires.
 * Table: archives
 */
class Archive extends Model
{
    protected string $table = 'archives';
    protected string $primaryKey = 'id_archive';
    protected array $fillable = [
        'titre',
        'description',
        'chemin_fichier',
        'hash_fichier', // SHA256
        'taille_fichier',
        'type_mime',
        'date_archivage',
        'archive_par', // user_id
        'annee_acad_id',
        'entite_type', // 'etudiant', 'enseignant', 'systeme'
        'entite_id',
    ];

    /**
     * Retourne l'utilisateur qui a archivé
     */
    public function getUtilisateur(): ?Utilisateur
    {
        if ($this->archive_par === null) {
            return null;
        }
        return Utilisateur::find((int) $this->archive_par);
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
        return $hashActuel === $this->hash_fichier;
    }

    /**
     * Crée une archive à partir d'un fichier physique
     */
    public static function creer(
        string $chemin,
        string $titre,
        int $userId,
        string $entiteType,
        int $entiteId
    ): ?self {
        if (!file_exists($chemin)) {
            return null;
        }

        $archive = new self([
            'titre' => $titre,
            'chemin_fichier' => $chemin,
            'hash_fichier' => hash_file('sha256', $chemin),
            'taille_fichier' => filesize($chemin),
            'type_mime' => mime_content_type($chemin),
            'date_archivage' => date('Y-m-d H:i:s'),
            'archive_par' => $userId,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
        ]);

        $archive->save();
        return $archive;
    }
}
