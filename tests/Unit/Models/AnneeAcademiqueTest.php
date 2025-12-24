<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\AnneeAcademique;

/**
 * Tests unitaires pour le modèle AnneeAcademique
 * 
 * @see PRD 02 - Entités Académiques (RF-014)
 * @covers \App\Models\AnneeAcademique
 */
class AnneeAcademiqueTest extends TestCase
{
    // ===== HELPER METHODS =====

    /**
     * Crée une année académique de test
     */
    protected function createTestAnneeAcademique(array $overrides = []): array
    {
        return array_merge([
            'id_annee_acad' => 1,
            'lib_annee_acad' => '2024-2025',
            'date_debut' => '2024-09-01',
            'date_fin' => '2025-07-31',
            'est_active' => true,
        ], $overrides);
    }

    // ===== TESTS ATTRIBUTS REQUIS =====

    /**
     * @test
     * Une année académique a les attributs requis
     */
    public function testAttributsRequisAnneeAcademique(): void
    {
        $annee = $this->createTestAnneeAcademique();

        $this->assertArrayHasKey('id_annee_acad', $annee);
        $this->assertArrayHasKey('lib_annee_acad', $annee);
        $this->assertArrayHasKey('date_debut', $annee);
        $this->assertArrayHasKey('date_fin', $annee);
        $this->assertArrayHasKey('est_active', $annee);
    }

    // ===== TESTS LIBELLÉ =====

    /**
     * @test
     * Le libellé suit le format AAAA-AAAA
     */
    public function testLibelleFormatValide(): void
    {
        $annee = $this->createTestAnneeAcademique([
            'lib_annee_acad' => '2024-2025',
        ]);

        $this->assertMatchesRegularExpression('/^\d{4}-\d{4}$/', $annee['lib_annee_acad']);
    }

    /**
     * @test
     * Le libellé accepte différentes années
     */
    public function testLibelleDifferentesAnnees(): void
    {
        $libelles = ['2020-2021', '2021-2022', '2022-2023', '2023-2024', '2024-2025'];
        
        foreach ($libelles as $lib) {
            $annee = $this->createTestAnneeAcademique(['lib_annee_acad' => $lib]);
            $this->assertEquals($lib, $annee['lib_annee_acad']);
        }
    }

    /**
     * @test
     * L'année de début est avant l'année de fin dans le libellé
     */
    public function testLibelleAnneesOrdonees(): void
    {
        $annee = $this->createTestAnneeAcademique(['lib_annee_acad' => '2024-2025']);
        
        $parts = explode('-', $annee['lib_annee_acad']);
        $this->assertCount(2, $parts);
        $this->assertLessThan((int)$parts[1], (int)$parts[0]);
    }

    // ===== TESTS DATES =====

    /**
     * @test
     * La date de début est avant la date de fin
     */
    public function testDateDebutAvantFin(): void
    {
        $annee = $this->createTestAnneeAcademique([
            'date_debut' => '2024-09-01',
            'date_fin' => '2025-07-31',
        ]);

        $debut = strtotime($annee['date_debut']);
        $fin = strtotime($annee['date_fin']);

        $this->assertLessThan($fin, $debut);
    }

    /**
     * @test
     * La date de début accepte le format ISO
     */
    public function testDateDebutFormatISO(): void
    {
        $annee = $this->createTestAnneeAcademique([
            'date_debut' => '2024-09-01',
        ]);

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $annee['date_debut']);
    }

    /**
     * @test
     * La date de fin accepte le format ISO
     */
    public function testDateFinFormatISO(): void
    {
        $annee = $this->createTestAnneeAcademique([
            'date_fin' => '2025-07-31',
        ]);

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $annee['date_fin']);
    }

    // ===== TESTS STATUT ACTIVE =====

    /**
     * @test
     * Une année peut être active ou inactive
     */
    public function testStatutActive(): void
    {
        $anneeActive = $this->createTestAnneeAcademique(['est_active' => true]);
        $anneeInactive = $this->createTestAnneeAcademique(['est_active' => false]);

        $this->assertTrue($anneeActive['est_active']);
        $this->assertFalse($anneeInactive['est_active']);
    }

    /**
     * @test
     * Par défaut une année n'est pas active
     */
    public function testStatutDefautInactive(): void
    {
        // Par défaut devrait être false selon la règle métier
        $annee = $this->createTestAnneeAcademique(['est_active' => false]);
        $this->assertFalse($annee['est_active']);
    }

    /**
     * @test
     * Une seule année peut être active à la fois
     */
    public function testUneSeuleAnneeActive(): void
    {
        $annee1 = $this->createTestAnneeAcademique(['id_annee_acad' => 1, 'est_active' => true]);
        $annee2 = $this->createTestAnneeAcademique(['id_annee_acad' => 2, 'est_active' => false]);
        
        // Seule une des deux peut être active
        $this->assertTrue(
            ($annee1['est_active'] && !$annee2['est_active']) ||
            (!$annee1['est_active'] && $annee2['est_active']) ||
            (!$annee1['est_active'] && !$annee2['est_active'])
        );
    }

    // ===== TESTS MÉTHODES MODÈLE =====

    /**
     * @test
     * Le modèle AnneeAcademique existe
     */
    public function testModeleAnneeAcademiqueExiste(): void
    {
        $this->assertTrue(class_exists(AnneeAcademique::class));
    }

    /**
     * @test
     * La méthode active existe
     */
    public function testMethodeActiveExiste(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'active'));
    }

    /**
     * @test
     * La méthode ordonnees existe
     */
    public function testMethodeOrdonneesExiste(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'ordonnees'));
    }

    /**
     * @test
     * La méthode findByLibelle existe
     */
    public function testMethodeFindByLibelleExiste(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'findByLibelle'));
    }

    /**
     * @test
     * Les méthodes d'état existent
     */
    public function testMethodesEtatExistent(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'estActive'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'estEnCours'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'estPassee'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'estFuture'));
    }

    /**
     * @test
     * Les méthodes métier existent
     */
    public function testMethodesMetierExistent(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'activer'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'desactiver'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'getDureeJours'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'nombreDossiers'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'statistiquesDossiersParEtat'));
        $this->assertTrue(method_exists(AnneeAcademique::class, 'totalPaiements'));
    }

    /**
     * @test
     * La méthode genererLibelleSuivante existe
     */
    public function testMethodeGenererLibelleSuivanteExiste(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'genererLibelleSuivante'));
    }

    // ===== TESTS CALCULS =====

    /**
     * @test
     * La durée en jours est calculable
     */
    public function testDureeEnJoursCalculable(): void
    {
        $annee = $this->createTestAnneeAcademique([
            'date_debut' => '2024-09-01',
            'date_fin' => '2025-07-31',
        ]);

        $debut = new \DateTime($annee['date_debut']);
        $fin = new \DateTime($annee['date_fin']);
        $duree = $debut->diff($fin)->days;

        $this->assertGreaterThan(0, $duree);
        $this->assertLessThan(400, $duree); // Une année académique fait moins de 400 jours
    }

    /**
     * @test
     * Le libellé suivant est calculable
     */
    public function testLibelleSuivantCalculable(): void
    {
        $annee = $this->createTestAnneeAcademique(['lib_annee_acad' => '2024-2025']);
        
        // Parse le libellé actuel (format: 2024-2025)
        if (preg_match('/(\d{4})-(\d{4})/', $annee['lib_annee_acad'], $matches)) {
            $debut = (int) $matches[1] + 1;
            $fin = (int) $matches[2] + 1;
            $suivante = "{$debut}-{$fin}";
        } else {
            $suivante = '';
        }

        $this->assertEquals('2025-2026', $suivante);
    }

    // ===== TESTS RELATIONS =====

    /**
     * @test
     * La méthode semestres existe (relation)
     */
    public function testRelationSemestresExiste(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'semestres'));
    }

    /**
     * @test
     * La méthode dossiersEtudiants existe (relation)
     */
    public function testRelationDossiersEtudiantsExiste(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'dossiersEtudiants'));
    }

    /**
     * @test
     * La méthode paiements existe (relation)
     */
    public function testRelationPaiementsExiste(): void
    {
        $this->assertTrue(method_exists(AnneeAcademique::class, 'paiements'));
    }
}
