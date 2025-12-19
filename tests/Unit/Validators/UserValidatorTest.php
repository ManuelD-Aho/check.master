<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\UserValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour UserValidator
 */
class UserValidatorTest extends TestCase
{
    private UserValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UserValidator();
    }

    /**
     * @test
     */
    public function testValidateCreationAvecDonneesValides(): void
    {
        $result = $this->validator->validate([
            'email' => 'user@example.com',
            'nom_utilisateur' => 'Jean Dupont',
            'password' => 'Password123',
            'groupe_id' => 1,
        ], true);
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateEmailObligatoire(): void
    {
        $result = $this->validator->validate([
            'email' => '',
            'nom_utilisateur' => 'Jean',
            'password' => 'Password123',
            'groupe_id' => 1,
        ], true);
        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateEmailInvalide(): void
    {
        $result = $this->validator->validate([
            'email' => 'invalid',
            'nom_utilisateur' => 'Jean',
            'password' => 'Password123',
            'groupe_id' => 1,
        ], true);
        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidatePasswordTropCourt(): void
    {
        $result = $this->validator->validate([
            'email' => 'user@example.com',
            'nom_utilisateur' => 'Jean',
            'password' => 'short',
            'groupe_id' => 1,
        ], true);
        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidatePasswordSansMajuscule(): void
    {
        $result = $this->validator->validate([
            'email' => 'user@example.com',
            'nom_utilisateur' => 'Jean',
            'password' => 'password123',
            'groupe_id' => 1,
        ], true);
        $this->assertFalse($result);
        $this->assertStringContainsString('majuscule', $this->validator->getErrors()['password']);
    }

    /**
     * @test
     */
    public function testValidatePasswordSansChiffre(): void
    {
        $result = $this->validator->validate([
            'email' => 'user@example.com',
            'nom_utilisateur' => 'Jean',
            'password' => 'PasswordABC',
            'groupe_id' => 1,
        ], true);
        $this->assertFalse($result);
        $this->assertStringContainsString('chiffre', $this->validator->getErrors()['password']);
    }

    /**
     * @test
     */
    public function testValidateGroupeObligatoire(): void
    {
        $result = $this->validator->validate([
            'email' => 'user@example.com',
            'nom_utilisateur' => 'Jean',
            'password' => 'Password123',
            'groupe_id' => null,
        ], true);
        $this->assertFalse($result);
        $this->assertArrayHasKey('groupe_id', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateModificationSansPassword(): void
    {
        $result = $this->validator->validate([
            'email' => 'user@example.com',
            'nom_utilisateur' => 'Jean',
            'groupe_id' => 1,
        ], false);
        $this->assertTrue($result);
    }
}
