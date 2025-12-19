<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\RapportValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour RapportValidator
 */
class RapportValidatorTest extends TestCase
{
    private RapportValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RapportValidator();
    }

    // =========================================================================
    // Tests validation données
    // =========================================================================

    /**
     * @test
     */
    public function testValidateAvecTitreValide(): void
    {
        $result = $this->validator->validate([
            'titre' => 'Implémentation d\'un système de gestion académique',
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateTitreObligatoire(): void
    {
        $result = $this->validator->validate([
            'titre' => '',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('titre', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateTitreTropCourt(): void
    {
        $result = $this->validator->validate([
            'titre' => 'Court',
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('10 caractères', $this->validator->getErrors()['titre']);
    }

    // =========================================================================
    // Tests validation fichier
    // =========================================================================

    /**
     * @test
     */
    public function testValidateFileErreurUpload(): void
    {
        $result = $this->validator->validateFile([
            'name' => 'rapport.pdf',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0,
            'tmp_name' => '',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('fichier', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateFileErreurTailleTropGrande(): void
    {
        $result = $this->validator->validateFile([
            'name' => 'rapport.pdf',
            'error' => UPLOAD_ERR_INI_SIZE,
            'size' => 0,
            'tmp_name' => '',
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('volumineux', $this->validator->getErrors()['fichier']);
    }

    /**
     * @test
     */
    public function testValidateFileErreurPartiel(): void
    {
        $result = $this->validator->validateFile([
            'name' => 'rapport.pdf',
            'error' => UPLOAD_ERR_PARTIAL,
            'size' => 0,
            'tmp_name' => '',
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('partiellement', $this->validator->getErrors()['fichier']);
    }

    /**
     * @test
     * @dataProvider extensionsNonAutorisees
     */
    public function testValidateFileExtensionNonAutorisee(string $extension): void
    {
        // Créer un fichier temporaire pour le test
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');

        $result = $this->validator->validateFile([
            'name' => "rapport.$extension",
            'error' => UPLOAD_ERR_OK,
            'size' => 1024,
            'tmp_name' => $tmpFile,
        ]);

        @unlink($tmpFile);

        $this->assertFalse($result);
        $this->assertStringContainsString('Format', $this->validator->getErrors()['fichier']);
    }

    public static function extensionsNonAutorisees(): array
    {
        return [
            ['exe'],
            ['php'],
            ['js'],
            ['html'],
            ['txt'],
        ];
    }

    // =========================================================================
    // Tests méthodes utilitaires
    // =========================================================================

    /**
     * @test
     */
    public function testGetErrors(): void
    {
        $this->validator->validate(['titre' => '']);

        $errors = $this->validator->getErrors();

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    /**
     * @test
     */
    public function testGetFirstError(): void
    {
        $this->validator->validate(['titre' => '']);

        $firstError = $this->validator->getFirstError();

        $this->assertNotNull($firstError);
        $this->assertIsString($firstError);
    }

    /**
     * @test
     */
    public function testGetFirstErrorRetourneNullSansErreur(): void
    {
        $this->validator->validate([
            'titre' => 'Titre suffisamment long pour être valide',
        ]);

        $this->assertNull($this->validator->getFirstError());
    }
}
