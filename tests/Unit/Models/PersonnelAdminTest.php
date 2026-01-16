<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\PersonnelAdmin;

class PersonnelAdminTest extends TestCase
{
    public function testMethodeFonctionExiste(): void
    {
        $this->assertTrue(method_exists(PersonnelAdmin::class, 'fonction'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(PersonnelAdmin::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new PersonnelAdmin([]);
        $this->assertEquals('personnel_admin', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(PersonnelAdmin::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new PersonnelAdmin([]);
        $this->assertEquals('id_pers_admin', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(PersonnelAdmin::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new PersonnelAdmin([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('nom_pers', $fillable);
        $this->assertContains('prenom_pers', $fillable);
        $this->assertContains('email_pers', $fillable);
        $this->assertContains('fonction_id', $fillable);
    }
}
