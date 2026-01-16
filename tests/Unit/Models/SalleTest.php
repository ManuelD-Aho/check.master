<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Salle;

class SalleTest extends TestCase
{
    public function testMethodeGetEquipementsExiste(): void
    {
        $this->assertTrue(method_exists(Salle::class, 'getEquipements'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Salle::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Salle([]);
        $this->assertEquals('salles', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Salle::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Salle([]);
        $this->assertEquals('id_salle', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Salle::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Salle([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('nom_salle', $fillable);
        $this->assertContains('batiment', $fillable);
        $this->assertContains('capacite', $fillable);
        $this->assertContains('equipement_json', $fillable);
    }
}
