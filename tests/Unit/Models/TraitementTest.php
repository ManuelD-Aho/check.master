<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Traitement;

class TraitementTest extends TestCase
{
    public function testMethodeFindByLibelleExiste(): void
    {
        $this->assertTrue(method_exists(Traitement::class, 'findByLibelle'));
        
        $reflection = new \ReflectionMethod(Traitement::class, 'findByLibelle');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Traitement::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Traitement([]);
        $this->assertEquals('traitement', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Traitement::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Traitement([]);
        $this->assertEquals('id_traitement', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Traitement::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Traitement([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_traitement', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('ordre_traitement', $fillable);
        $this->assertContains('actif', $fillable);
    }
}
