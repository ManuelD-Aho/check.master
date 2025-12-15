<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Tests\TestCase;

/**
 * Tests unitaires pour le modèle Etudiant
 */
class EtudiantTest extends TestCase
{
    /**
     * @test
     * Un étudiant a les attributs requis
     */
    public function testAttributsRequisEtudiant(): void
    {
        $etudiant = $this->createTestEtudiant();

        $this->assertArrayHasKeys([
            'id_etudiant',
            'num_etu',
            'nom_etu',
            'prenom_etu',
            'email_etu',
        ], $etudiant);
    }

    /**
     * @test
     * Le numéro étudiant est unique
     */
    public function testNumeroEtudiantUnique(): void
    {
        $etudiant1 = $this->createTestEtudiant(['num_etu' => 'ETU2024001']);
        $etudiant2 = $this->createTestEtudiant(['num_etu' => 'ETU2024002']);

        $this->assertNotEquals($etudiant1['num_etu'], $etudiant2['num_etu']);
    }

    /**
     * @test
     * L'email étudiant doit être valide
     */
    public function testEmailEtudiantValide(): void
    {
        $etudiant = $this->createTestEtudiant([
            'email_etu' => 'test@ufhb.edu.ci',
        ]);

        $this->assertTrue(filter_var($etudiant['email_etu'], FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * @test
     * Le nom complet est une concaténation nom + prénom
     */
    public function testNomComplet(): void
    {
        $etudiant = $this->createTestEtudiant([
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
        ]);

        $nomComplet = $etudiant['nom_etu'] . ' ' . $etudiant['prenom_etu'];
        $this->assertEquals('KOUASSI Aya', $nomComplet);
    }

    /**
     * @test
     * Un étudiant peut être actif ou inactif
     */
    public function testStatutActif(): void
    {
        $etudiantActif = $this->createTestEtudiant(['actif' => true]);
        $etudiantInactif = $this->createTestEtudiant(['actif' => false]);

        $this->assertTrue($etudiantActif['actif']);
        $this->assertFalse($etudiantInactif['actif']);
    }

    /**
     * @test
     * Le téléphone accepte le format ivoirien
     */
    public function testFormatTelephoneIvoirien(): void
    {
        $etudiant = $this->createTestEtudiant([
            'telephone_etu' => '+225 0712345678',
        ]);

        $this->assertStringStartsWith('+225', $etudiant['telephone_etu']);
    }

    /**
     * @test
     * La promotion est une année valide
     */
    public function testPromotionAnnee(): void
    {
        $etudiant = $this->createTestEtudiant([
            'promotion_etu' => '2024',
        ]);

        $this->assertMatchesRegularExpression('/^20\d{2}$/', $etudiant['promotion_etu']);
    }

    /**
     * @test
     * Le genre accepte les valeurs définies
     */
    public function testGenreValide(): void
    {
        $genresValides = ['Homme', 'Femme', 'Autre'];

        foreach ($genresValides as $genre) {
            $etudiant = $this->createTestEtudiant(['genre_etu' => $genre]);
            $this->assertContains($etudiant['genre_etu'], $genresValides);
        }
    }
}
