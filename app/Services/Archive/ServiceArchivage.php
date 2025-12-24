<?php

declare(strict_types=1);

namespace App\Services\Archive;

use App\Models\Archive;
use App\Models\DocumentGenere;
use App\Services\Security\ServiceAudit;
use Src\Exceptions\ArchiveException;
use Src\Exceptions\NotFoundException;

/**
 * Service Archivage
 * 
 * Gestion de l'archivage des documents avec intégrité SHA256.
 * Verrouillage des archives et vérification périodique.
 */
class ServiceArchivage
{
    /**
     * Archive un document généré
     */
    public static function archiver(int $documentId, ?int $utilisateurId = null): Archive
    {
        $document = DocumentGenere::find($documentId);
        if ($document === null) {
            throw new NotFoundException('Document non trouvé');
        }

        // Vérifier l'intégrité du document avant archivage
        if (!$document->verifierIntegrite()) {
            throw new ArchiveException('Le document est corrompu et ne peut pas être archivé');
        }

        // Créer l'archive
        $archive = Archive::creerDepuisDocument($documentId);
        if ($archive === null) {
            throw new ArchiveException('Impossible de créer l\'archive');
        }

        ServiceAudit::log('archivage_document', 'archive', $archive->getId(), [
            'document_id' => $documentId,
            'hash' => $document->hash_sha256,
        ]);

        return $archive;
    }

    /**
     * Vérifie l'intégrité d'une archive
     */
    public static function verifierIntegrite(int $archiveId): bool
    {
        $archive = Archive::find($archiveId);
        if ($archive === null) {
            throw new NotFoundException('Archive non trouvée');
        }

        $integre = $archive->verifierIntegrite();

        ServiceAudit::log('verification_archive', 'archive', $archiveId, [
            'integre' => $integre,
        ]);

        return $integre;
    }

    /**
     * Verrouille une archive
     */
    public static function verrouiller(int $archiveId, int $utilisateurId): bool
    {
        $archive = Archive::find($archiveId);
        if ($archive === null) {
            throw new NotFoundException('Archive non trouvée');
        }

        $archive->verrouiller();

        ServiceAudit::log('verrouillage_archive', 'archive', $archiveId);

        return true;
    }

    /**
     * Déverrouille une archive (admin uniquement)
     */
    public static function deverrouiller(int $archiveId, int $utilisateurId, string $motif): bool
    {
        $archive = Archive::find($archiveId);
        if ($archive === null) {
            throw new NotFoundException('Archive non trouvée');
        }

        $archive->deverrouiller();

        ServiceAudit::log('deverrouillage_archive', 'archive', $archiveId, [
            'motif' => $motif,
            'admin_id' => $utilisateurId,
        ]);

        return true;
    }

    /**
     * Retourne les archives nécessitant une vérification
     */
    public static function getArchivesAVerifier(int $joursDepuisVerification = 30): array
    {
        return Archive::aVerifier($joursDepuisVerification);
    }

    /**
     * Exécute une vérification en masse de toutes les archives
     */
    public static function verifierToutesArchives(): array
    {
        $resultats = Archive::verifierToutesArchives();

        ServiceAudit::log('verification_masse_archives', 'systeme', null, [
            'total' => $resultats['total'],
            'integres' => $resultats['integres'],
            'corrompues' => $resultats['corrompues'],
        ]);

        return $resultats;
    }

    /**
     * Retourne les statistiques d'archivage
     */
    public static function getStatistiques(): array
    {
        return Archive::statistiques();
    }
}
