<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\NiveauAccesDonnees;

class NiveauAccesDonneesTest extends TestCase
{
    public function testConstantesNiveauxDefinies(): void
    {
        $this->assertEquals('Public', NiveauAccesDonnees::NIVEAU_PUBLIC);
        $this->assertEquals('Confidentiel', NiveauAccesDonnees::NIVEAU_CONFIDENTIEL);
        $this->assertEquals('Restreint', NiveauAccesDonnees::NIVEAU_RESTREINT);
        $this->assertEquals('Secret', NiveauAccesDonnees::NIVEAU_SECRET);
    }

    public function testMethodeFindByLibelleExiste(): void
    {
        $this->assertTrue(method_exists(NiveauAccesDonnees::class, 'findByLibelle'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(NiveauAccesDonnees::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new NiveauAccesDonnees([]);
        $this->assertEquals('niveau_acces_donnees', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(NiveauAccesDonnees::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new NiveauAccesDonnees([]);
        $this->assertEquals('id_niv_acces_donnee', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(NiveauAccesDonnees::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new NiveauAccesDonnees([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_niveau_acces', $fillable);
        $this->assertContains('description', $fillable);
    }
}
