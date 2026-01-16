<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\EscaladeNiveau;

class EscaladeNiveauTest extends TestCase
{
    public function testMethodeFindByNiveauExiste(): void
    {
        $this->assertTrue(method_exists(EscaladeNiveau::class, 'findByNiveau'));
        
        $reflection = new \ReflectionMethod(EscaladeNiveau::class, 'findByNiveau');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(EscaladeNiveau::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new EscaladeNiveau([]);
        $this->assertEquals('escalade_niveaux', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(EscaladeNiveau::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new EscaladeNiveau([]);
        $this->assertEquals('id_niveau', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(EscaladeNiveau::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new EscaladeNiveau([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('niveau', $fillable);
        $this->assertContains('nom_niveau', $fillable);
        $this->assertContains('delai_reponse_jours', $fillable);
    }
}
