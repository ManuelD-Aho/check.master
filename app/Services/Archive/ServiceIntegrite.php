<?php

declare(strict_types=1);

namespace App\Services\Archive;

use App\Models\Archive;
use App\Models\DocumentGenere;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;

/**
 * Service Intégrité
 * 
 * Vérification périodique de l'intégrité des documents archivés.
 * Détection de corruption et alertes.
 */
class ServiceIntegrite
{
    /**
     * Vérifie l'intégrité de tous les documents
     */
    public static function verifierTout(): array
    {
        $resultats = [
            'total' => 0,
            'integres' => 0,
            'corrompus' => [],
            'manquants' => [],
        ];

        $documents = DocumentGenere::all();
        $resultats['total'] = count($documents);

        foreach ($documents as $document) {
            if (!$document->fichierExiste()) {
                $resultats['manquants'][] = [
                    'id' => $document->getId(),
                    'chemin' => $document->chemin_fichier,
                    'type' => $document->type_document,
                ];
            } elseif (!$document->verifierIntegrite()) {
                $resultats['corrompus'][] = [
                    'id' => $document->getId(),
                    'chemin' => $document->chemin_fichier,
                    'type' => $document->type_document,
                    'hash_attendu' => $document->hash_sha256,
                    'hash_actuel' => hash_file('sha256', $document->chemin_fichier),
                ];
            } else {
                $resultats['integres']++;
            }
        }

        ServiceAudit::log('verification_integrite', 'systeme', null, [
            'total' => $resultats['total'],
            'integres' => $resultats['integres'],
            'corrompus' => count($resultats['corrompus']),
            'manquants' => count($resultats['manquants']),
        ]);

        // Alerter si problèmes détectés
        if (!empty($resultats['corrompus']) || !empty($resultats['manquants'])) {
            self::alerterProblemes($resultats);
        }

        return $resultats;
    }

    /**
     * Vérifie l'intégrité d'un document spécifique
     */
    public static function verifierDocument(int $documentId): array
    {
        $document = DocumentGenere::find($documentId);
        if ($document === null) {
            return ['erreur' => 'Document non trouvé'];
        }

        if (!$document->fichierExiste()) {
            return [
                'integre' => false,
                'erreur' => 'Fichier manquant',
            ];
        }

        $hashActuel = hash_file('sha256', $document->chemin_fichier);
        $integre = $hashActuel === $document->hash_sha256;

        return [
            'integre' => $integre,
            'hash_attendu' => $document->hash_sha256,
            'hash_actuel' => $hashActuel,
        ];
    }

    /**
     * Recalcule et met à jour le hash d'un document
     */
    public static function recalculerHash(int $documentId): ?string
    {
        $document = DocumentGenere::find($documentId);
        if ($document === null || !$document->fichierExiste()) {
            return null;
        }

        $nouveauHash = hash_file('sha256', $document->chemin_fichier);
        $document->hash_sha256 = $nouveauHash;
        $document->save();

        ServiceAudit::log('recalcul_hash', 'document', $documentId, [
            'nouveau_hash' => $nouveauHash,
        ]);

        return $nouveauHash;
    }

    /**
     * Alerte les administrateurs en cas de problèmes d'intégrité
     */
    private static function alerterProblemes(array $resultats): void
    {
        $message = "Problèmes d'intégrité détectés:\n";
        $message .= "- Documents corrompus: " . count($resultats['corrompus']) . "\n";
        $message .= "- Fichiers manquants: " . count($resultats['manquants']) . "\n";

        // Envoyer une notification aux administrateurs
        ServiceNotification::envoyerParCode(
            'alerte_integrite',
            [], // Liste des admins à récupérer
            [
                'corrompus' => count($resultats['corrompus']),
                'manquants' => count($resultats['manquants']),
                'message' => $message,
            ]
        );
    }

    /**
     * Vérifie les archives non vérifiées depuis X jours
     */
    public static function verifierArchivesObsoletes(int $jours = 30): array
    {
        $archives = Archive::aVerifier($jours);
        $resultats = ['verifiees' => 0, 'problemes' => []];

        foreach ($archives as $archive) {
            $integre = $archive->verifierIntegrite();
            $resultats['verifiees']++;

            if (!$integre) {
                $resultats['problemes'][] = $archive->getId();
            }
        }

        return $resultats;
    }
}
