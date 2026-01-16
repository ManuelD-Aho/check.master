<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\NiveauApprobation;

class NiveauApprobationTest extends TestCase
{
    public function testMethodeFindByLibelleExiste(): void
    {
        $this->assertTrue(method_exists(NiveauApprobation::class, 'findByLibelle'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(NiveauApprobation::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new NiveauApprobation([]);
        $this->assertEquals('niveau_approbation', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(NiveauApprobation::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new NiveauApprobation([]);
        $this->assertEquals('id_niveau_approbation', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(NiveauApprobation::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new NiveauApprobation([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_niveau', $fillable);
        $this->assertContains('ordre_niveau', $fillable);
    }
}
