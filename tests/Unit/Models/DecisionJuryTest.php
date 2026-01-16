<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\DecisionJury;

class DecisionJuryTest extends TestCase
{
    public function testConstantesDecisionsDefinies(): void
    {
        $this->assertEquals('Admis', DecisionJury::DECISION_ADMIS);
        $this->assertEquals('Ajourné', DecisionJury::DECISION_AJOURNE);
        $this->assertEquals('Corrections_mineures', DecisionJury::DECISION_CORRECTIONS_MINEURES);
        $this->assertEquals('Corrections_majeures', DecisionJury::DECISION_CORRECTIONS_MAJEURES);
    }

    public function testConstantesDelaisDefinies(): void
    {
        $this->assertEquals(7, DecisionJury::DELAI_CORRECTIONS_MINEURES);
        $this->assertEquals(30, DecisionJury::DELAI_CORRECTIONS_MAJEURES);
    }

    public function testMethodeSoutenanceExiste(): void
    {
        $this->assertTrue(method_exists(DecisionJury::class, 'soutenance'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(DecisionJury::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new DecisionJury([]);
        $this->assertEquals('decisions_jury', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(DecisionJury::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new DecisionJury([]);
        $this->assertEquals('id_decision', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(DecisionJury::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new DecisionJury([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('soutenance_id', $fillable);
        $this->assertContains('decision', $fillable);
        $this->assertContains('delai_corrections', $fillable);
        $this->assertContains('commentaires', $fillable);
    }
}
