<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\AuthValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour AuthValidator
 */
class AuthValidatorTest extends TestCase
{
    private AuthValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new AuthValidator();
    }

    // =========================================================================
    // Tests validateLogin
    // =========================================================================

    /**
     * @test
     */
    public function testValidateLoginAvecDonneesValides(): void
    {
        $result = $this->validator->validateLogin([
            'login' => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateLoginAvecLoginVide(): void
    {
        $result = $this->validator->validateLogin([
            'login' => '',
            'password' => 'password123',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('login', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateLoginAvecPasswordVide(): void
    {
        $result = $this->validator->validateLogin([
            'login' => 'user@example.com',
            'password' => '',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateLoginAvecDeuxChampsVides(): void
    {
        $result = $this->validator->validateLogin([
            'login' => '',
            'password' => '',
        ]);

        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertArrayHasKey('login', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    // =========================================================================
    // Tests validateRegister
    // =========================================================================

    /**
     * @test
     */
    public function testValidateRegisterAvecDonneesValides(): void
    {
        $result = $this->validator->validateRegister([
            'email' => 'user@example.com',
            'password' => 'Password123',
            'confirm_password' => 'Password123',
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateRegisterAvecEmailInvalide(): void
    {
        $result = $this->validator->validateRegister([
            'email' => 'invalid-email',
            'password' => 'Password123',
            'confirm_password' => 'Password123',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateRegisterAvecPasswordTropCourt(): void
    {
        $result = $this->validator->validateRegister([
            'email' => 'user@example.com',
            'password' => 'short',
            'confirm_password' => 'short',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateRegisterAvecPasswordsNonCorrespondants(): void
    {
        $result = $this->validator->validateRegister([
            'email' => 'user@example.com',
            'password' => 'Password123',
            'confirm_password' => 'DifferentPassword',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('confirm_password', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateRegisterAvecEmailVide(): void
    {
        $result = $this->validator->validateRegister([
            'email' => '',
            'password' => 'Password123',
            'confirm_password' => 'Password123',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testGetErrorsRetourneLesErreurs(): void
    {
        $this->validator->validateLogin([]);

        $errors = $this->validator->getErrors();

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }
}
