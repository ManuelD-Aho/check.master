<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\RoleTemporaire;

class RoleTemporaireTest extends TestCase
{
    public function testConstantesRolesDefinies(): void
    {
        $this->assertEquals('president_jury', RoleTemporaire::ROLE_PRESIDENT_JURY);
        $this->assertEquals('membre_jury', RoleTemporaire::ROLE_MEMBRE_JURY);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(RoleTemporaire::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new RoleTemporaire([]);
        $this->assertEquals('roles_temporaires', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(RoleTemporaire::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new RoleTemporaire([]);
        $this->assertEquals('id_role_temp', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(RoleTemporaire::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new RoleTemporaire([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('role_code', $fillable);
        $this->assertContains('contexte_type', $fillable);
        $this->assertContains('actif', $fillable);
        $this->assertContains('valide_de', $fillable);
        $this->assertContains('valide_jusqu_a', $fillable);
    }
}
