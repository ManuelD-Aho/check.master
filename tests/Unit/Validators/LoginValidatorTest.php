<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;
use App\Validators\LoginValidator;

/**
 * Tests unitaires pour LoginValidator
 */
class LoginValidatorTest extends TestCase
{
    private LoginValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LoginValidator();
    }

    /**
     * Test de validation avec données valides
     */
    public function testValidationDonneesValides(): void
    {
        $result = $this->validator->valider([
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErreurs());
    }

    /**
     * Test de validation avec email vide
     */
    public function testValidationEmailVide(): void
    {
        $result = $this->validator->valider([
            'email' => '',
            'password' => 'password123',
        ]);

        $this->assertFalse($result);
        $this->assertTrue($this->validator->aErreur('email'));
    }

    /**
     * Test de validation avec email invalide
     */
    public function testValidationEmailInvalide(): void
    {
        $result = $this->validator->valider([
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $this->assertFalse($result);
        $this->assertTrue($this->validator->aErreur('email'));
        $this->assertStringContainsString('valide', $this->validator->getErreur('email'));
    }

    /**
     * Test de validation avec mot de passe vide
     */
    public function testValidationMotDePasseVide(): void
    {
        $result = $this->validator->valider([
            'email' => 'user@example.com',
            'password' => '',
        ]);

        $this->assertFalse($result);
        $this->assertTrue($this->validator->aErreur('password'));
    }

    /**
     * Test de validation avec les deux champs manquants
     */
    public function testValidationDeuxChampsManquants(): void
    {
        $result = $this->validator->valider([]);

        $this->assertFalse($result);
        $this->assertTrue($this->validator->aErreur('email'));
        $this->assertTrue($this->validator->aErreur('password'));
        $this->assertCount(2, $this->validator->getErreurs());
    }

    /**
     * Test de la première erreur
     */
    public function testPremiereErreur(): void
    {
        $this->validator->valider([
            'email' => '',
            'password' => '',
        ]);

        $premiere = $this->validator->getPremiereErreur();
        $this->assertNotNull($premiere);
        $this->assertIsString($premiere);
    }
}
