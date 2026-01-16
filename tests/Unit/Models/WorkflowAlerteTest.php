<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\WorkflowAlerte;

class WorkflowAlerteTest extends TestCase
{
    public function testConstantesTypesAlertesDefinies(): void
    {
        $this->assertEquals('50_pourcent', WorkflowAlerte::TYPE_50_POURCENT);
        $this->assertEquals('80_pourcent', WorkflowAlerte::TYPE_80_POURCENT);
        $this->assertEquals('100_pourcent', WorkflowAlerte::TYPE_100_POURCENT);
    }

    public function testMethodeDossierExiste(): void
    {
        $this->assertTrue(method_exists(WorkflowAlerte::class, 'dossier'));
    }

    public function testMethodeEtatExiste(): void
    {
        $this->assertTrue(method_exists(WorkflowAlerte::class, 'etat'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(WorkflowAlerte::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new WorkflowAlerte([]);
        $this->assertEquals('workflow_alertes', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(WorkflowAlerte::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new WorkflowAlerte([]);
        $this->assertEquals('id_alerte', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(WorkflowAlerte::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new WorkflowAlerte([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('dossier_id', $fillable);
        $this->assertContains('etat_id', $fillable);
        $this->assertContains('type_alerte', $fillable);
        $this->assertContains('envoyee', $fillable);
        $this->assertContains('envoyee_le', $fillable);
    }
}
