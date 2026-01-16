<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\HistoriqueEntite;

class HistoriqueEntiteTest extends TestCase
{
    public function testMethodeModifieParExiste(): void
    {
        $this->assertTrue(method_exists(HistoriqueEntite::class, 'modifiePar'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(HistoriqueEntite::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new HistoriqueEntite([]);
        $this->assertEquals('historique_entites', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(HistoriqueEntite::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new HistoriqueEntite([]);
        $this->assertEquals('id_historique', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(HistoriqueEntite::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new HistoriqueEntite([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('entite_type', $fillable);
        $this->assertContains('entite_id', $fillable);
        $this->assertContains('version', $fillable);
        $this->assertContains('snapshot_json', $fillable);
        $this->assertContains('modifie_par', $fillable);
    }
}
