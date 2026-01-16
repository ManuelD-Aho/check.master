<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Penalite;

class PenaliteTest extends TestCase
{
    public function testConstantesTypesDefinies(): void
    {
        $this->assertEquals('Retard', Penalite::TYPE_RETARD);
        $this->assertEquals('Absence', Penalite::TYPE_ABSENCE);
        $this->assertEquals('Document_manquant', Penalite::TYPE_DOCUMENT);
        $this->assertEquals('Autre', Penalite::TYPE_AUTRE);
    }

    public function testMethodeGetEtudiantExiste(): void
    {
        $this->assertTrue(method_exists(Penalite::class, 'getEtudiant'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Penalite::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Penalite([]);
        $this->assertEquals('penalites', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Penalite::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Penalite([]);
        $this->assertEquals('id_penalite', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Penalite::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Penalite([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('etudiant_id', $fillable);
        $this->assertContains('annee_acad_id', $fillable);
        $this->assertContains('montant', $fillable);
        $this->assertContains('motif', $fillable);
        $this->assertContains('type', $fillable);
    }
}
