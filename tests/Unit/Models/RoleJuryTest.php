<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\RoleJury;

class RoleJuryTest extends TestCase
{
    public function testConstantesRolesDefinies(): void
    {
        $this->assertEquals('PRESIDENT', RoleJury::ROLE_PRESIDENT);
        $this->assertEquals('RAPPORTEUR', RoleJury::ROLE_RAPPORTEUR);
        $this->assertEquals('EXAMINATEUR', RoleJury::ROLE_EXAMINATEUR);
        $this->assertEquals('ENCADREUR', RoleJury::ROLE_ENCADREUR);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(RoleJury::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new RoleJury([]);
        $this->assertEquals('roles_jury', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(RoleJury::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new RoleJury([]);
        $this->assertEquals('id_role', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(RoleJury::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new RoleJury([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('code_role', $fillable);
        $this->assertContains('libelle_role', $fillable);
        $this->assertContains('ordre_affichage', $fillable);
    }
}
