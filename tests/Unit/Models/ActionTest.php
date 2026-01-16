<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Action;

class ActionTest extends TestCase
{
    public function testConstantesActionDefinies(): void
    {
        $this->assertEquals('Lire', Action::ACTION_LIRE);
        $this->assertEquals('Creer', Action::ACTION_CREER);
        $this->assertEquals('Modifier', Action::ACTION_MODIFIER);
        $this->assertEquals('Supprimer', Action::ACTION_SUPPRIMER);
        $this->assertEquals('Valider', Action::ACTION_VALIDER);
        $this->assertEquals('Exporter', Action::ACTION_EXPORTER);
    }

    public function testMethodeFindByLibelleExiste(): void
    {
        $this->assertTrue(method_exists(Action::class, 'findByLibelle'));
        
        $reflection = new \ReflectionMethod(Action::class, 'findByLibelle');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testMethodeToutesExiste(): void
    {
        $this->assertTrue(method_exists(Action::class, 'toutes'));
        
        $reflection = new \ReflectionMethod(Action::class, 'toutes');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeCreerExiste(): void
    {
        $this->assertTrue(method_exists(Action::class, 'creer'));
        
        $reflection = new \ReflectionMethod(Action::class, 'creer');
        $params = $reflection->getParameters();
        
        $this->assertCount(2, $params);
        $this->assertEquals('libelle', $params[0]->getName());
        $this->assertEquals('description', $params[1]->getName());
    }

    public function testMethodeEstActionCRUDExiste(): void
    {
        $this->assertTrue(method_exists(Action::class, 'estActionCRUD'));
    }

    public function testActionCRUDValeurs(): void
    {
        $actionsCRUD = [
            Action::ACTION_LIRE,
            Action::ACTION_CREER,
            Action::ACTION_MODIFIER,
            Action::ACTION_SUPPRIMER,
        ];

        $this->assertCount(4, $actionsCRUD);
        $this->assertContains('Lire', $actionsCRUD);
        $this->assertContains('Creer', $actionsCRUD);
        $this->assertContains('Modifier', $actionsCRUD);
        $this->assertContains('Supprimer', $actionsCRUD);
    }

    public function testActionNonCRUD(): void
    {
        $actionsNonCRUD = [
            Action::ACTION_VALIDER,
            Action::ACTION_EXPORTER,
        ];

        $this->assertNotContains('Lire', $actionsNonCRUD);
        $this->assertContains('Valider', $actionsNonCRUD);
        $this->assertContains('Exporter', $actionsNonCRUD);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Action::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $action = new Action([]);
        $this->assertEquals('action', $property->getValue($action));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Action::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $action = new Action([]);
        $this->assertEquals('id_action', $property->getValue($action));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Action::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $action = new Action([]);
        $fillable = $property->getValue($action);
        
        $this->assertContains('lib_action', $fillable);
        $this->assertContains('description', $fillable);
    }
}
