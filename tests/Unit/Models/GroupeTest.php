<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Groupe;

class GroupeTest extends TestCase
{
    public function testMethodeUtilisateursGroupesExiste(): void
    {
        $this->assertTrue(method_exists(Groupe::class, 'utilisateursGroupes'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Groupe::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Groupe([]);
        $this->assertEquals('groupes', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Groupe::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Groupe([]);
        $this->assertEquals('id_groupe', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Groupe::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Groupe([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('nom_groupe', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('niveau_hierarchique', $fillable);
        $this->assertContains('actif', $fillable);
    }
}
