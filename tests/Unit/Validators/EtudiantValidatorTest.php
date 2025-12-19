<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\EtudiantValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour EtudiantValidator
 */
class EtudiantValidatorTest extends TestCase
{
    private EtudiantValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new EtudiantValidator();
    }

    // =========================================================================
    // Tests données valides
    // =========================================================================

    /**
     * @test
     */
    public function testValidateAvecDonneesValidesCreation(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
            'email_etu' => 'aya.kouassi@ufhb.edu.ci',
            'telephone_etu' => '+2250712345678',
            'date_naiss_etu' => '2000-01-01',
            'genre_etu' => 'Femme',
        ], true);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateAvecDonneesValidesModification(): void
    {
        $result = $this->validator->validate([
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
        ], false);

        $this->assertTrue($result);
    }

    // =========================================================================
    // Tests numéro étudiant
    // =========================================================================

    /**
     * @test
     */
    public function testValidateNumEtuObligatoireEnCreation(): void
    {
        $result = $this->validator->validate([
            'num_etu' => '',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
        ], true);

        $this->assertFalse($result);
        $this->assertArrayHasKey('num_etu', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateNumEtuFormatInvalide(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'INVALID',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
        ], true);

        $this->assertFalse($result);
        $this->assertStringContainsString('Format', $this->validator->getErrors()['num_etu']);
    }

    /**
     * @test
     */
    public function testValidateNumEtuFormatValide(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'CI01552852',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
        ], true);

        $this->assertTrue($result);
    }

    // =========================================================================
    // Tests nom et prénom
    // =========================================================================

    /**
     * @test
     */
    public function testValidateNomObligatoire(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => '',
            'prenom_etu' => 'Aya',
        ], true);

        $this->assertFalse($result);
        $this->assertArrayHasKey('nom_etu', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateNomTropCourt(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'K',
            'prenom_etu' => 'Aya',
        ], true);

        $this->assertFalse($result);
        $this->assertStringContainsString('2 caractères', $this->validator->getErrors()['nom_etu']);
    }

    /**
     * @test
     */
    public function testValidatePrenomObligatoire(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => '',
        ], true);

        $this->assertFalse($result);
        $this->assertArrayHasKey('prenom_etu', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests email
    // =========================================================================

    /**
     * @test
     */
    public function testValidateEmailFormatInvalide(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
            'email_etu' => 'invalid-email',
        ], true);

        $this->assertFalse($result);
        $this->assertArrayHasKey('email_etu', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateEmailFacultatif(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
        ], true);

        $this->assertTrue($result);
    }

    // =========================================================================
    // Tests téléphone
    // =========================================================================

    /**
     * @test
     */
    public function testValidateTelephoneFormatInvalide(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
            'telephone_etu' => '123',
        ], true);

        $this->assertFalse($result);
        $this->assertArrayHasKey('telephone_etu', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateTelephoneFormatValide(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
            'telephone_etu' => '0712345678',
        ], true);

        $this->assertTrue($result);
    }

    // =========================================================================
    // Tests date de naissance
    // =========================================================================

    /**
     * @test
     */
    public function testValidateDateNaissanceFormatInvalide(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
            'date_naiss_etu' => '01/01/2000',
        ], true);

        $this->assertFalse($result);
        $this->assertStringContainsString('Format', $this->validator->getErrors()['date_naiss_etu']);
    }

    /**
     * @test
     */
    public function testValidateDateNaissanceDansFutur(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
            'date_naiss_etu' => '2099-01-01',
        ], true);

        $this->assertFalse($result);
        $this->assertStringContainsString('futur', $this->validator->getErrors()['date_naiss_etu']);
    }

    // =========================================================================
    // Tests genre
    // =========================================================================

    /**
     * @test
     */
    public function testValidateGenreValide(): void
    {
        foreach (['Homme', 'Femme', 'Autre'] as $genre) {
            $result = $this->validator->validate([
                'num_etu' => 'AB12345678',
                'nom_etu' => 'KOUASSI',
                'prenom_etu' => 'Aya',
                'genre_etu' => $genre,
            ], true);

            $this->assertTrue($result, "Genre '$genre' devrait être valide");
        }
    }

    /**
     * @test
     */
    public function testValidateGenreInvalide(): void
    {
        $result = $this->validator->validate([
            'num_etu' => 'AB12345678',
            'nom_etu' => 'KOUASSI',
            'prenom_etu' => 'Aya',
            'genre_etu' => 'Invalid',
        ], true);

        $this->assertFalse($result);
        $this->assertArrayHasKey('genre_etu', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests erreurs
    // =========================================================================

    /**
     * @test
     */
    public function testGetFirstError(): void
    {
        $this->validator->validate([
            'nom_etu' => '',
            'prenom_etu' => '',
        ], true);

        $this->assertNotNull($this->validator->getFirstError());
    }
}
