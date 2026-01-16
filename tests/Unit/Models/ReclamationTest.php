<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Reclamation;

class ReclamationTest extends TestCase
{
    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Reclamation::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Reclamation([]);
        $this->assertEquals('reclamations', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Reclamation::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Reclamation([]);
        $this->assertEquals('id_reclamation', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Reclamation::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Reclamation([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('etudiant_id', $fillable);
        $this->assertContains('type_reclamation', $fillable);
        $this->assertContains('sujet', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('statut', $fillable);
    }
}
