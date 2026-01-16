<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\WorkflowHistorique;

class WorkflowHistoriqueTest extends TestCase
{
    public function testMethodeDossierExiste(): void
    {
        $this->assertTrue(method_exists(WorkflowHistorique::class, 'dossier'));
    }

    public function testMethodeEtatSourceExiste(): void
    {
        $this->assertTrue(method_exists(WorkflowHistorique::class, 'etatSource'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(WorkflowHistorique::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new WorkflowHistorique([]);
        $this->assertEquals('workflow_historique', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(WorkflowHistorique::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new WorkflowHistorique([]);
        $this->assertEquals('id_historique', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(WorkflowHistorique::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new WorkflowHistorique([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('dossier_id', $fillable);
        $this->assertContains('etat_source_id', $fillable);
        $this->assertContains('etat_cible_id', $fillable);
        $this->assertContains('transition_id', $fillable);
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('commentaire', $fillable);
        $this->assertContains('snapshot_json', $fillable);
    }
}
