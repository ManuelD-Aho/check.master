<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Classe de base pour tous les tests
 * 
 * Fournit des méthodes utilitaires communes et la configuration des tests.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Configuration avant chaque test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Réinitialiser l'état de l'authentification
        if (class_exists(\Src\Support\Auth::class)) {
            \Src\Support\Auth::reset();
        }
    }

    /**
     * Nettoyage après chaque test
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Crée un mock PDO pour les tests de base de données
     */
    protected function createMockPdo(): \PDO
    {
        return new \PDO('sqlite::memory:');
    }

    /**
     * Helper pour créer un utilisateur de test
     * 
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function createTestUser(array $overrides = []): array
    {
        return array_merge([
            'id_utilisateur' => 1,
            'nom_utilisateur' => 'Test User',
            'login_utilisateur' => 'test@example.com',
            'mdp_utilisateur' => password_hash('password123', PASSWORD_ARGON2ID),
            'id_type_utilisateur' => 1,
            'id_GU' => 1,
            'statut_utilisateur' => 'Actif',
            'doit_changer_mdp' => false,
        ], $overrides);
    }

    /**
     * Helper pour créer un étudiant de test
     * 
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function createTestEtudiant(array $overrides = []): array
    {
        return array_merge([
            'id_etudiant' => 1,
            'num_etu' => 'ETU2024001',
            'nom_etu' => 'Dupont',
            'prenom_etu' => 'Jean',
            'email_etu' => 'jean.dupont@ufhb.edu.ci',
            'telephone_etu' => '+225 0712345678',
            'promotion_etu' => '2024',
            'actif' => true,
        ], $overrides);
    }

    /**
     * Helper pour créer une candidature de test
     * 
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function createTestCandidature(array $overrides = []): array
    {
        return array_merge([
            'id_candidature' => 1,
            'dossier_id' => 1,
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Martin',
            'maitre_stage_email' => 'martin@entreprise.ci',
            'date_soumission' => date('Y-m-d H:i:s'),
            'validee_scolarite' => false,
            'validee_communication' => false,
        ], $overrides);
    }

    /**
     * Assert qu'un tableau contient certaines clés
     * 
     * @param array<string> $keys
     * @param array<string, mixed> $array
     */
    protected function assertArrayHasKeys(array $keys, array $array): void
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array, "La clé '$key' est manquante dans le tableau.");
        }
    }
}
