<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Migration;

class MigrationTest extends TestCase
{
    public function testMethodeFindByNameExiste(): void
    {
        $this->assertTrue(method_exists(Migration::class, 'findByName'));
        
        $reflection = new \ReflectionMethod(Migration::class, 'findByName');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeEstExecuteeExiste(): void
    {
        $this->assertTrue(method_exists(Migration::class, 'estExecutee'));
        
        $reflection = new \ReflectionMethod(Migration::class, 'estExecutee');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Migration::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Migration([]);
        $this->assertEquals('migrations', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Migration::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Migration([]);
        $this->assertEquals('id_migration', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Migration::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Migration([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('migration_name', $fillable);
        $this->assertContains('executed_at', $fillable);
    }
}
