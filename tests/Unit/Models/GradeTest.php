<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Grade;

class GradeTest extends TestCase
{
    public function testMethodeEnseignantsExiste(): void
    {
        $this->assertTrue(method_exists(Grade::class, 'enseignants'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Grade::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Grade([]);
        $this->assertEquals('grades', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Grade::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Grade([]);
        $this->assertEquals('id_grade', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Grade::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Grade([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_grade', $fillable);
        $this->assertContains('niveau_hierarchique', $fillable);
        $this->assertContains('actif', $fillable);
    }
}
