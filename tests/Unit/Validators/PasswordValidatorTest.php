<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;
use App\Validators\PasswordValidator;

/**
 * Tests unitaires pour PasswordValidator
 */
class PasswordValidatorTest extends TestCase
{
    private PasswordValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PasswordValidator();
    }

    /**
     * Test de mot de passe valide
     */
    public function testMotDePasseValide(): void
    {
        $result = $this->validator->valider('MonPass123!');

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErreurs());
    }

    /**
     * Test de mot de passe trop court
     */
    public function testMotDePasseTropCourt(): void
    {
        $result = $this->validator->valider('Pass1!');

        $this->assertFalse($result);
        $this->assertNotEmpty($this->validator->getErreurs());
    }

    /**
     * Test de mot de passe sans majuscule
     */
    public function testMotDePasseSansMajuscule(): void
    {
        $result = $this->validator->valider('monpassword123!');

        $this->assertFalse($result);
        $this->assertStringContainsString('majuscule', $this->validator->getErreursFormatees());
    }

    /**
     * Test de mot de passe sans chiffre
     */
    public function testMotDePasseSansChiffre(): void
    {
        $result = $this->validator->valider('MonPassword!');

        $this->assertFalse($result);
        $this->assertStringContainsString('chiffre', $this->validator->getErreursFormatees());
    }

    /**
     * Test de mot de passe sans caractère spécial
     */
    public function testMotDePasseSansCaractereSpecial(): void
    {
        $result = $this->validator->valider('MonPassword123');

        $this->assertFalse($result);
        $this->assertStringContainsString('spécial', $this->validator->getErreursFormatees());
    }

    /**
     * Test de validation de confirmation correcte
     */
    public function testConfirmationCorrecte(): void
    {
        $result = $this->validator->validerConfirmation('MonPass123!', 'MonPass123!');

        $this->assertTrue($result);
    }

    /**
     * Test de validation de confirmation incorrecte
     */
    public function testConfirmationIncorrecte(): void
    {
        $result = $this->validator->validerConfirmation('MonPass123!', 'AutrePass123!');

        $this->assertFalse($result);
        $this->assertStringContainsString('correspondent', $this->validator->getErreursFormatees());
    }

    /**
     * Test de détection de mot de passe commun
     */
    public function testMotDePasseCommun(): void
    {
        $this->assertTrue($this->validator->estMotDePasseCommun('password'));
        $this->assertTrue($this->validator->estMotDePasseCommun('P@ssw0rd'));
        $this->assertFalse($this->validator->estMotDePasseCommun('UnMotDePasseUnique123!'));
    }

    /**
     * Test du calcul de force
     */
    public function testCalculForce(): void
    {
        // Mot de passe faible
        $forceF = $this->validator->calculerForce('pass');
        $this->assertLessThan(50, $forceF);

        // Mot de passe fort
        $forceForte = $this->validator->calculerForce('MonSuperMotDePasse123!@#');
        $this->assertGreaterThan(80, $forceForte);
    }
}
