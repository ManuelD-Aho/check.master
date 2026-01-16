<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Semestre;

class SemestreTest extends TestCase
{
    public function testMethodeAnneeAcademiqueExiste(): void
    {
        $this->assertTrue(method_exists(Semestre::class, 'anneeAcademique'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Semestre::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Semestre([]);
        $this->assertEquals('semestre', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Semestre::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Semestre([]);
        $this->assertEquals('id_semestre', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Semestre::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Semestre([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_semestre', $fillable);
        $this->assertContains('annee_acad_id', $fillable);
        $this->assertContains('date_debut', $fillable);
        $this->assertContains('date_fin', $fillable);
    }
}
