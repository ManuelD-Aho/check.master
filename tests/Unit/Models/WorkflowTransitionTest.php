<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\WorkflowTransition;

class WorkflowTransitionTest extends TestCase
{
    public function testMethodeEtatSourceExiste(): void
    {
        $this->assertTrue(method_exists(WorkflowTransition::class, 'etatSource'));
    }

    public function testMethodeEtatCibleExiste(): void
    {
        $this->assertTrue(method_exists(WorkflowTransition::class, 'etatCible'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(WorkflowTransition::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new WorkflowTransition([]);
        $this->assertEquals('workflow_transitions', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(WorkflowTransition::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new WorkflowTransition([]);
        $this->assertEquals('id_transition', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(WorkflowTransition::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new WorkflowTransition([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('etat_source_id', $fillable);
        $this->assertContains('etat_cible_id', $fillable);
        $this->assertContains('code_transition', $fillable);
        $this->assertContains('nom_transition', $fillable);
        $this->assertContains('roles_autorises', $fillable);
        $this->assertContains('notifier', $fillable);
    }
}
