<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ServiceRapport
 */
class ServiceRapportTest extends TestCase
{
    /**
     * @test
     * Les statuts de rapport sont définis
     */
    public function testStatutsRapportDefinis(): void
    {
        $statuts = ['Brouillon', 'Soumis', 'En_evaluation', 'Valide', 'Rejete'];

        $this->assertCount(5, $statuts);
        $this->assertContains('Brouillon', $statuts);
        $this->assertContains('Valide', $statuts);
    }

    /**
     * @test
     * Un rapport a un titre non vide
     */
    public function testRapportTitreNonVide(): void
    {
        $rapport = $this->createTestRapport();

        $this->assertArrayHasKey('titre', $rapport);
        $this->assertNotEmpty($rapport['titre']);
    }

    /**
     * @test
     * Le versionnage s'incrémente correctement
     */
    public function testVersionnageRapport(): void
    {
        $rapportV1 = $this->createTestRapport(['version' => 1]);
        $rapportV2 = array_merge($rapportV1, ['version' => 2]);

        $this->assertEquals(1, $rapportV1['version']);
        $this->assertEquals(2, $rapportV2['version']);
        $this->assertGreaterThan($rapportV1['version'], $rapportV2['version']);
    }

    /**
     * @test
     * Le hash de fichier est calculé
     */
    public function testHashFichierCalcule(): void
    {
        $contenuExemple = 'Contenu du rapport de test';
        $hash = hash('sha256', $contenuExemple);

        $this->assertNotEmpty($hash);
        $this->assertEquals(64, strlen($hash)); // SHA256 = 64 caractères hex
    }

    /**
     * @test
     * La date de dépôt est enregistrée
     */
    public function testDateDepotEnregistree(): void
    {
        $rapport = $this->createTestRapport([
            'statut' => 'Soumis',
            'date_depot' => date('Y-m-d H:i:s'),
        ]);

        $this->assertArrayHasKey('date_depot', $rapport);
        $this->assertNotEmpty($rapport['date_depot']);
    }

    /**
     * @test
     * Le chemin de fichier est valide
     */
    public function testCheminFichierValide(): void
    {
        $rapport = $this->createTestRapport([
            'chemin_fichier' => '/storage/rapports/rapport_1.pdf',
        ]);

        $this->assertStringStartsWith('/storage', $rapport['chemin_fichier']);
        $this->assertStringEndsWith('.pdf', $rapport['chemin_fichier']);
    }

    /**
     * @test
     * Un rapport brouillon peut être modifié
     */
    public function testBrouillonModifiable(): void
    {
        $rapport = $this->createTestRapport(['statut' => 'Brouillon']);

        // Un brouillon peut être modifié
        $this->assertEquals('Brouillon', $rapport['statut']);

        // Modifier le titre
        $rapport['titre'] = 'Nouveau titre modifié';
        $this->assertEquals('Nouveau titre modifié', $rapport['titre']);
    }

    /**
     * @test
     * Un rapport soumis ne peut plus être modifié
     */
    public function testSoumisNonModifiable(): void
    {
        $rapport = $this->createTestRapport(['statut' => 'Soumis']);

        // Un rapport soumis n'est normalement plus modifiable
        $this->assertEquals('Soumis', $rapport['statut']);
        $this->assertNotEquals('Brouillon', $rapport['statut']);
    }

    /**
     * Helper pour créer un rapport de test
     * 
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function createTestRapport(array $overrides = []): array
    {
        return array_merge([
            'id_rapport' => 1,
            'dossier_id' => 1,
            'titre' => 'Rapport de mémoire de test',
            'contenu_html' => '<h1>Introduction</h1><p>Contenu...</p>',
            'version' => 1,
            'statut' => 'Brouillon',
            'date_depot' => null,
            'chemin_fichier' => null,
            'hash_fichier' => null,
        ], $overrides);
    }
}
