<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\CritereEvaluation;

class CritereEvaluationTest extends TestCase
{
    public function testMethodeFindByCodeExiste(): void
    {
        $this->assertTrue(method_exists(CritereEvaluation::class, 'findByCode'));
        
        $reflection = new \ReflectionMethod(CritereEvaluation::class, 'findByCode');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(CritereEvaluation::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new CritereEvaluation([]);
        $this->assertEquals('critere_evaluation', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(CritereEvaluation::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new CritereEvaluation([]);
        $this->assertEquals('id_critere', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(CritereEvaluation::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new CritereEvaluation([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('code_critere', $fillable);
        $this->assertContains('libelle', $fillable);
        $this->assertContains('ponderation', $fillable);
        $this->assertContains('actif', $fillable);
    }
}
