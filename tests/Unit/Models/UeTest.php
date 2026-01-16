<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Ue;

class UeTest extends TestCase
{
    public function testMethodeNiveauExiste(): void
    {
        $this->assertTrue(method_exists(Ue::class, 'niveau'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Ue::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Ue([]);
        $this->assertEquals('ue', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Ue::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Ue([]);
        $this->assertEquals('id_ue', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Ue::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Ue([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('code_ue', $fillable);
        $this->assertContains('lib_ue', $fillable);
        $this->assertContains('credits', $fillable);
        $this->assertContains('niveau_id', $fillable);
        $this->assertContains('semestre_id', $fillable);
    }
}
