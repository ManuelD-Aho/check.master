<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Specialite;

class SpecialiteTest extends TestCase
{
    public function testMethodeEnseignantsExiste(): void
    {
        $this->assertTrue(method_exists(Specialite::class, 'enseignants'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Specialite::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Specialite([]);
        $this->assertEquals('specialites', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Specialite::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Specialite([]);
        $this->assertEquals('id_specialite', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Specialite::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Specialite([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_specialite', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('actif', $fillable);
    }
}
