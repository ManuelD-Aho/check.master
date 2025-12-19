<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\ReclamationValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ReclamationValidator
 */
class ReclamationValidatorTest extends TestCase
{
    private ReclamationValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ReclamationValidator();
    }

    /**
     * @test
     */
    public function testValidateAvecDonneesValides(): void
    {
        $result = $this->validator->validate([
            'sujet' => 'Problème de paiement',
            'message' => 'Je n\'arrive pas à effectuer mon paiement.',
            'type_reclamation' => 'paiement',
        ]);
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateSujetTropCourt(): void
    {
        $result = $this->validator->validate([
            'sujet' => 'AB',
            'message' => 'Un message long.',
            'type_reclamation' => 'paiement',
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('sujet', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateMessageTropCourt(): void
    {
        $result = $this->validator->validate([
            'sujet' => 'Sujet valide',
            'message' => 'Court',
            'type_reclamation' => 'paiement',
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('message', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateTypeReclamationObligatoire(): void
    {
        $result = $this->validator->validate([
            'sujet' => 'Sujet valide',
            'message' => 'Message long suffisant.',
            'type_reclamation' => '',
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('type_reclamation', $this->validator->getErrors());
    }
}
