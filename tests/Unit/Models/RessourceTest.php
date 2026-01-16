<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Ressource;

class RessourceTest extends TestCase
{
    public function testMethodePermissionsExiste(): void
    {
        $this->assertTrue(method_exists(Ressource::class, 'permissions'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Ressource::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Ressource([]);
        $this->assertEquals('ressources', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Ressource::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Ressource([]);
        $this->assertEquals('id_ressource', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Ressource::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Ressource([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('code_ressource', $fillable);
        $this->assertContains('nom_ressource', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('module', $fillable);
    }
}
