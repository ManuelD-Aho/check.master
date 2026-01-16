<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Rattacher;

class RattacherTest extends TestCase
{
    public function testMethodeGroupeUtilisateurExiste(): void
    {
        $this->assertTrue(method_exists(Rattacher::class, 'groupeUtilisateur'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Rattacher::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Rattacher([]);
        $this->assertEquals('rattacher', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Rattacher::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Rattacher([]);
        $this->assertEquals('id_rattacher', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Rattacher::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Rattacher([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('id_GU', $fillable);
        $this->assertContains('id_traitement', $fillable);
        $this->assertContains('id_action', $fillable);
    }
}
