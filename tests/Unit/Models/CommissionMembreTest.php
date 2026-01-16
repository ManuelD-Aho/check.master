<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\CommissionMembre;

class CommissionMembreTest extends TestCase
{
    public function testConstantesRolesDefinies(): void
    {
        $this->assertEquals('President', CommissionMembre::ROLE_PRESIDENT);
        $this->assertEquals('Rapporteur', CommissionMembre::ROLE_RAPPORTEUR);
        $this->assertEquals('Membre', CommissionMembre::ROLE_MEMBRE);
    }

    public function testMethodeSessionExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'session'));
    }

    public function testMethodeEnseignantExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'enseignant'));
    }

    public function testMethodePourSessionExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'pourSession'));
        
        $reflection = new \ReflectionMethod(CommissionMembre::class, 'pourSession');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeEstMembreExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'estMembre'));
        
        $reflection = new \ReflectionMethod(CommissionMembre::class, 'estMembre');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodePresidentExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'president'));
        
        $reflection = new \ReflectionMethod(CommissionMembre::class, 'president');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeEstPresentExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'estPresent'));
    }

    public function testMethodeEstPresidentExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'estPresident'));
    }

    public function testMethodeEstRapporteurExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'estRapporteur'));
    }

    public function testMethodeMarquerPresentExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'marquerPresent'));
    }

    public function testMethodeMarquerAbsentExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'marquerAbsent'));
    }

    public function testMethodeMarquerDepartExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'marquerDepart'));
    }

    public function testMethodeDefinirPresidentExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'definirPresident'));
    }

    public function testMethodeDefinirRapporteurExiste(): void
    {
        $this->assertTrue(method_exists(CommissionMembre::class, 'definirRapporteur'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(CommissionMembre::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new CommissionMembre([]);
        $this->assertEquals('commission_membres', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(CommissionMembre::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new CommissionMembre([]);
        $this->assertEquals('id_membre', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(CommissionMembre::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new CommissionMembre([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('session_id', $fillable);
        $this->assertContains('enseignant_id', $fillable);
        $this->assertContains('role', $fillable);
        $this->assertContains('present', $fillable);
    }
}
