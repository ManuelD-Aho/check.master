<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Entreprise;

class EntrepriseTest extends TestCase
{
    public function testMethodeCandidaturesExiste(): void
    {
        $this->assertTrue(method_exists(Entreprise::class, 'candidatures'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Entreprise::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Entreprise([]);
        $this->assertEquals('entreprises', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Entreprise::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Entreprise([]);
        $this->assertEquals('id_entreprise', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Entreprise::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Entreprise([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('nom_entreprise', $fillable);
        $this->assertContains('secteur_activite', $fillable);
        $this->assertContains('adresse', $fillable);
        $this->assertContains('telephone', $fillable);
        $this->assertContains('email', $fillable);
    }
}
