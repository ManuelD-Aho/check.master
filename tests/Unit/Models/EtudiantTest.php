<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Tests\TestCase;

/**
 * Tests unitaires pour le modèle Etudiant
 * 
 * @see PRD 02 - Entités Académiques (RF-010)
 * @covers \App\Models\Etudiant
 */
class EtudiantTest extends TestCase
{
    // ===== TESTS ATTRIBUTS REQUIS =====

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

    // ===== TESTS VALIDATION EMAIL =====

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
     * L'email accepte le domaine universitaire
     */
    public function testEmailDomaineUniversitaire(): void
    {
        $etudiant = $this->createTestEtudiant([
            'email_etu' => 'etudiant@ufhb.edu.ci',
        ]);

        $this->assertStringEndsWith('.ci', $etudiant['email_etu']);
    }

    // ===== TESTS NOM =====

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
     * Le nom formel est NOM Prénom en majuscules
     */
    public function testNomFormel(): void
    {
        $etudiant = $this->createTestEtudiant([
            'nom_etu' => 'Kouassi',
            'prenom_etu' => 'Aya',
        ]);

        $nomFormel = strtoupper($etudiant['nom_etu']) . ' ' . $etudiant['prenom_etu'];
        $this->assertEquals('KOUASSI Aya', $nomFormel);
    }

    // ===== TESTS STATUT =====

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
     * Par défaut un étudiant est actif
     */
    public function testStatutDefautActif(): void
    {
        $etudiant = $this->createTestEtudiant();
        $this->assertTrue($etudiant['actif']);
    }

    // ===== TESTS TÉLÉPHONE =====

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
     * Le téléphone peut être vide
     */
    public function testTelephonePeutEtreVide(): void
    {
        $etudiant = $this->createTestEtudiant([
            'telephone_etu' => null,
        ]);

        $this->assertNull($etudiant['telephone_etu']);
    }

    // ===== TESTS PROMOTION =====

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
     * La promotion accepte différentes années
     */
    public function testPromotionDifferentesAnnees(): void
    {
        $promotions = ['2020', '2021', '2022', '2023', '2024', '2025'];
        
        foreach ($promotions as $promo) {
            $etudiant = $this->createTestEtudiant(['promotion_etu' => $promo]);
            $this->assertEquals($promo, $etudiant['promotion_etu']);
        }
    }

    // ===== TESTS GENRE =====

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

    /**
     * @test
     * Le genre homme est valide
     */
    public function testGenreHomme(): void
    {
        $etudiant = $this->createTestEtudiant(['genre_etu' => 'Homme']);
        $this->assertEquals('Homme', $etudiant['genre_etu']);
    }

    /**
     * @test
     * Le genre femme est valide
     */
    public function testGenreFemme(): void
    {
        $etudiant = $this->createTestEtudiant(['genre_etu' => 'Femme']);
        $this->assertEquals('Femme', $etudiant['genre_etu']);
    }

    // ===== TESTS DATE DE NAISSANCE =====

    /**
     * @test
     * La date de naissance est optionnelle
     */
    public function testDateNaissanceOptionnelle(): void
    {
        $etudiant = $this->createTestEtudiant(['date_naiss_etu' => null]);
        $this->assertNull($etudiant['date_naiss_etu']);
    }

    /**
     * @test
     * La date de naissance accepte le format standard
     */
    public function testDateNaissanceFormatStandard(): void
    {
        $etudiant = $this->createTestEtudiant(['date_naiss_etu' => '2000-05-15']);
        $this->assertEquals('2000-05-15', $etudiant['date_naiss_etu']);
    }

    // ===== TESTS LIEU DE NAISSANCE =====

    /**
     * @test
     * Le lieu de naissance est optionnel
     */
    public function testLieuNaissanceOptionnel(): void
    {
        $etudiant = $this->createTestEtudiant(['lieu_naiss_etu' => null]);
        $this->assertNull($etudiant['lieu_naiss_etu']);
    }

    /**
     * @test
     * Le lieu de naissance accepte les villes ivoiriennes
     */
    public function testLieuNaissanceVilleIvoirienne(): void
    {
        $villes = ['Abidjan', 'Bouaké', 'Yamoussoukro', 'San-Pédro', 'Korhogo'];
        
        foreach ($villes as $ville) {
            $etudiant = $this->createTestEtudiant(['lieu_naiss_etu' => $ville]);
            $this->assertEquals($ville, $etudiant['lieu_naiss_etu']);
        }
    }

    // ===== TESTS NUMÉRO CARTE =====

    /**
     * @test
     * Le numéro carte a un format alphanumérique
     */
    public function testNumeroCarteFormatAlphanum(): void
    {
        $numeros = ['CI01552852', 'ETU2024001', 'M2MIAGE001'];
        
        foreach ($numeros as $num) {
            $etudiant = $this->createTestEtudiant(['num_etu' => $num]);
            $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $etudiant['num_etu']);
        }
    }

    /**
     * @test
     * Le numéro carte a une longueur maximale de 20 caractères
     */
    public function testNumeroCarteLongueurMax(): void
    {
        $etudiant = $this->createTestEtudiant(['num_etu' => 'ETU2024001']);
        $this->assertLessThanOrEqual(20, strlen($etudiant['num_etu']));
    }
}
