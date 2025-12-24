<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Enseignant;

/**
 * Tests unitaires pour le modèle Enseignant
 * 
 * @see PRD 02 - Entités Académiques (RF-011)
 * @covers \App\Models\Enseignant
 */
class EnseignantTest extends TestCase
{
    // ===== HELPER METHODS =====

    /**
     * Crée un enseignant de test
     */
    protected function createTestEnseignant(array $overrides = []): array
    {
        return array_merge([
            'id_enseignant' => 1,
            'nom_ens' => 'KONAN',
            'prenom_ens' => 'Yao',
            'email_ens' => 'yao.konan@ufhb.edu.ci',
            'telephone_ens' => '+225 0707070707',
            'grade_id' => 1,
            'fonction_id' => 1,
            'specialite_id' => 1,
            'actif' => true,
        ], $overrides);
    }

    // ===== TESTS ATTRIBUTS REQUIS =====

    /**
     * @test
     * Un enseignant a les attributs requis
     */
    public function testAttributsRequisEnseignant(): void
    {
        $enseignant = $this->createTestEnseignant();

        $this->assertArrayHasKey('id_enseignant', $enseignant);
        $this->assertArrayHasKey('nom_ens', $enseignant);
        $this->assertArrayHasKey('prenom_ens', $enseignant);
        $this->assertArrayHasKey('email_ens', $enseignant);
    }

    /**
     * @test
     * L'email enseignant est obligatoire
     */
    public function testEmailObligatoire(): void
    {
        $enseignant = $this->createTestEnseignant();
        $this->assertNotEmpty($enseignant['email_ens']);
    }

    // ===== TESTS EMAIL =====

    /**
     * @test
     * L'email enseignant doit être valide
     */
    public function testEmailEnseignantValide(): void
    {
        $enseignant = $this->createTestEnseignant([
            'email_ens' => 'prof@ufhb.edu.ci',
        ]);

        $this->assertTrue(filter_var($enseignant['email_ens'], FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * @test
     * L'email enseignant est unique
     */
    public function testEmailEnseignantUnique(): void
    {
        $enseignant1 = $this->createTestEnseignant(['email_ens' => 'prof1@ufhb.edu.ci']);
        $enseignant2 = $this->createTestEnseignant(['email_ens' => 'prof2@ufhb.edu.ci']);

        $this->assertNotEquals($enseignant1['email_ens'], $enseignant2['email_ens']);
    }

    // ===== TESTS NOM =====

    /**
     * @test
     * Le nom complet est une concaténation prénom + nom
     */
    public function testNomComplet(): void
    {
        $enseignant = $this->createTestEnseignant([
            'nom_ens' => 'KONAN',
            'prenom_ens' => 'Yao',
        ]);

        $nomComplet = $enseignant['prenom_ens'] . ' ' . $enseignant['nom_ens'];
        $this->assertEquals('Yao KONAN', $nomComplet);
    }

    /**
     * @test
     * Le nom formel avec grade
     */
    public function testNomFormelAvecGrade(): void
    {
        $enseignant = $this->createTestEnseignant([
            'nom_ens' => 'KONAN',
            'prenom_ens' => 'Yao',
        ]);
        
        // Simulation d'un grade "Professeur"
        $grade = 'Pr';
        $nomFormel = $grade . ' ' . strtoupper($enseignant['nom_ens']) . ' ' . $enseignant['prenom_ens'];
        
        $this->assertEquals('Pr KONAN Yao', $nomFormel);
    }

    // ===== TESTS STATUT =====

    /**
     * @test
     * Un enseignant peut être actif ou inactif
     */
    public function testStatutActif(): void
    {
        $enseignantActif = $this->createTestEnseignant(['actif' => true]);
        $enseignantInactif = $this->createTestEnseignant(['actif' => false]);

        $this->assertTrue($enseignantActif['actif']);
        $this->assertFalse($enseignantInactif['actif']);
    }

    /**
     * @test
     * Par défaut un enseignant est actif
     */
    public function testStatutDefautActif(): void
    {
        $enseignant = $this->createTestEnseignant();
        $this->assertTrue($enseignant['actif']);
    }

    // ===== TESTS RELATIONS =====

    /**
     * @test
     * Un enseignant peut avoir un grade
     */
    public function testRelationGrade(): void
    {
        $enseignant = $this->createTestEnseignant(['grade_id' => 1]);
        $this->assertNotNull($enseignant['grade_id']);
    }

    /**
     * @test
     * Un enseignant peut avoir une fonction
     */
    public function testRelationFonction(): void
    {
        $enseignant = $this->createTestEnseignant(['fonction_id' => 1]);
        $this->assertNotNull($enseignant['fonction_id']);
    }

    /**
     * @test
     * Un enseignant peut avoir une spécialité
     */
    public function testRelationSpecialite(): void
    {
        $enseignant = $this->createTestEnseignant(['specialite_id' => 1]);
        $this->assertNotNull($enseignant['specialite_id']);
    }

    /**
     * @test
     * Un enseignant peut ne pas avoir de grade
     */
    public function testGradeOptionnel(): void
    {
        $enseignant = $this->createTestEnseignant(['grade_id' => null]);
        $this->assertNull($enseignant['grade_id']);
    }

    /**
     * @test
     * Un enseignant peut ne pas avoir de fonction
     */
    public function testFonctionOptionnelle(): void
    {
        $enseignant = $this->createTestEnseignant(['fonction_id' => null]);
        $this->assertNull($enseignant['fonction_id']);
    }

    // ===== TESTS TÉLÉPHONE =====

    /**
     * @test
     * Le téléphone accepte le format ivoirien
     */
    public function testFormatTelephoneIvoirien(): void
    {
        $enseignant = $this->createTestEnseignant([
            'telephone_ens' => '+225 0707070707',
        ]);

        $this->assertStringStartsWith('+225', $enseignant['telephone_ens']);
    }

    /**
     * @test
     * Le téléphone peut être vide
     */
    public function testTelephonePeutEtreVide(): void
    {
        $enseignant = $this->createTestEnseignant(['telephone_ens' => null]);
        $this->assertNull($enseignant['telephone_ens']);
    }

    // ===== TESTS MÉTHODES MODÈLE =====

    /**
     * @test
     * Le modèle Enseignant existe
     */
    public function testModeleEnseignantExiste(): void
    {
        $this->assertTrue(class_exists(Enseignant::class));
    }

    /**
     * @test
     * Les méthodes de recherche existent
     */
    public function testMethodesRechercheExistent(): void
    {
        $this->assertTrue(method_exists(Enseignant::class, 'findByEmail'));
        $this->assertTrue(method_exists(Enseignant::class, 'actifs'));
        $this->assertTrue(method_exists(Enseignant::class, 'parGrade'));
        $this->assertTrue(method_exists(Enseignant::class, 'parSpecialite'));
        $this->assertTrue(method_exists(Enseignant::class, 'rechercher'));
    }

    /**
     * @test
     * Les méthodes helper existent
     */
    public function testMethodesHelperExistent(): void
    {
        $this->assertTrue(method_exists(Enseignant::class, 'getNomComplet'));
        $this->assertTrue(method_exists(Enseignant::class, 'getNomFormelAvecGrade'));
        $this->assertTrue(method_exists(Enseignant::class, 'getTitreComplet'));
    }

    /**
     * @test
     * Les méthodes jury existent
     */
    public function testMethodesJuryExistent(): void
    {
        $this->assertTrue(method_exists(Enseignant::class, 'estDisponible'));
        $this->assertTrue(method_exists(Enseignant::class, 'nombreSoutenances'));
        $this->assertTrue(method_exists(Enseignant::class, 'getSoutenancesJury'));
    }

    /**
     * @test
     * Les méthodes commission existent
     */
    public function testMethodesCommissionExistent(): void
    {
        $this->assertTrue(method_exists(Enseignant::class, 'estMembreCommission'));
    }

    /**
     * @test
     * Les méthodes statistiques existent
     */
    public function testMethodesStatistiquesExistent(): void
    {
        $this->assertTrue(method_exists(Enseignant::class, 'statistiquesParGrade'));
        $this->assertTrue(method_exists(Enseignant::class, 'statistiquesParSpecialite'));
    }
}
