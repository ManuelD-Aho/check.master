<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\NoteValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour NoteValidator
 */
class NoteValidatorTest extends TestCase
{
    private NoteValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new NoteValidator();
    }

    // =========================================================================
    // Tests données valides
    // =========================================================================

    /**
     * @test
     */
    public function testValidateAvecNotesValides(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => 15,
            'note_presentation' => 14,
            'note_travail' => 16,
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateAvecNotesLimites(): void
    {
        // Test avec 0 (minimum)
        $result = $this->validator->validate([
            'note_contenu' => 0,
            'note_presentation' => 0,
            'note_travail' => 0,
        ]);
        $this->assertTrue($result);

        // Test avec 20 (maximum)
        $result = $this->validator->validate([
            'note_contenu' => 20,
            'note_presentation' => 20,
            'note_travail' => 20,
        ]);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testValidateAvecNotesDecimales(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => 15.5,
            'note_presentation' => 14.25,
            'note_travail' => 16.75,
        ]);

        $this->assertTrue($result);
    }

    // =========================================================================
    // Tests note contenu
    // =========================================================================

    /**
     * @test
     */
    public function testValidateNoteContenuObligatoire(): void
    {
        $result = $this->validator->validate([
            'note_presentation' => 14,
            'note_travail' => 16,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('note_contenu', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateNoteContenuTropBasse(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => -1,
            'note_presentation' => 14,
            'note_travail' => 16,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('note_contenu', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateNoteContenuTropHaute(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => 21,
            'note_presentation' => 14,
            'note_travail' => 16,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('note_contenu', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests note présentation
    // =========================================================================

    /**
     * @test
     */
    public function testValidateNotePresentationObligatoire(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => 15,
            'note_travail' => 16,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('note_presentation', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateNotePresentationInvalide(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => 15,
            'note_presentation' => 'abc',
            'note_travail' => 16,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('note_presentation', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests note travail
    // =========================================================================

    /**
     * @test
     */
    public function testValidateNoteTravailObligatoire(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => 15,
            'note_presentation' => 14,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('note_travail', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateNoteTravailHorsLimite(): void
    {
        $result = $this->validator->validate([
            'note_contenu' => 15,
            'note_presentation' => 14,
            'note_travail' => 25,
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('0 et 20', $this->validator->getErrors()['note_travail']);
    }

    // =========================================================================
    // Tests erreurs
    // =========================================================================

    /**
     * @test
     */
    public function testGetFirstError(): void
    {
        $this->validator->validate([]);

        $this->assertNotNull($this->validator->getFirstError());
        $this->assertIsString($this->validator->getFirstError());
    }
}
