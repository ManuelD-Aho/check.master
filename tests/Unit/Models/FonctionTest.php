<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Fonction;

class FonctionTest extends TestCase
{
    public function testMethodeEnseignantsExiste(): void
    {
        $this->assertTrue(method_exists(Fonction::class, 'enseignants'));
    }

    public function testMethodePersonnelAdminExiste(): void
    {
        $this->assertTrue(method_exists(Fonction::class, 'personnelAdmin'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Fonction::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Fonction([]);
        $this->assertEquals('fonctions', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Fonction::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Fonction([]);
        $this->assertEquals('id_fonction', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Fonction::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Fonction([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_fonction', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('actif', $fillable);
    }
}
