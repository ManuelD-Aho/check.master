<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\SoutenanceValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour SoutenanceValidator
 */
class SoutenanceValidatorTest extends TestCase
{
    private SoutenanceValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new SoutenanceValidator();
    }

    /**
     * @test
     */
    public function testValidateAvecDonneesValides(): void
    {
        $result = $this->validator->validate([
            'dossier_id' => 1,
            'date_soutenance' => (new \DateTime('+1 week'))->format('Y-m-d'),
            'heure_debut' => '09:00',
            'heure_fin' => '11:00',
            'salle_id' => 1,
        ]);
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateDossierObligatoire(): void
    {
        $result = $this->validator->validate([
            'dossier_id' => null,
            'date_soutenance' => '2025-12-20',
            'heure_debut' => '09:00',
            'heure_fin' => '11:00',
            'salle_id' => 1,
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('dossier_id', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateDateObligatoire(): void
    {
        $result = $this->validator->validate([
            'dossier_id' => 1,
            'date_soutenance' => '',
            'heure_debut' => '09:00',
            'heure_fin' => '11:00',
            'salle_id' => 1,
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('date_soutenance', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateHeureDebutObligatoire(): void
    {
        $result = $this->validator->validate([
            'dossier_id' => 1,
            'date_soutenance' => '2025-12-20',
            'heure_debut' => '',
            'heure_fin' => '11:00',
            'salle_id' => 1,
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('heure_debut', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateHeureFinAvantDebut(): void
    {
        $result = $this->validator->validate([
            'dossier_id' => 1,
            'date_soutenance' => '2025-12-20',
            'heure_debut' => '14:00',
            'heure_fin' => '10:00',
            'salle_id' => 1,
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('heure_fin', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateSalleObligatoire(): void
    {
        $result = $this->validator->validate([
            'dossier_id' => 1,
            'date_soutenance' => '2025-12-20',
            'heure_debut' => '09:00',
            'heure_fin' => '11:00',
            'salle_id' => null,
        ]);
        $this->assertFalse($result);
        $this->assertArrayHasKey('salle_id', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testGetFirstError(): void
    {
        $this->validator->validate([]);
        $this->assertNotNull($this->validator->getFirstError());
    }
}
