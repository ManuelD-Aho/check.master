<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\StatutJury;

class StatutJuryTest extends TestCase
{
    public function testConstantesStatutsDefinies(): void
    {
        $this->assertEquals('Invité', StatutJury::STATUT_INVITE);
        $this->assertEquals('Accepté', StatutJury::STATUT_ACCEPTE);
        $this->assertEquals('Refusé', StatutJury::STATUT_REFUSE);
        $this->assertEquals('Absent', StatutJury::STATUT_ABSENT);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(StatutJury::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new StatutJury([]);
        $this->assertEquals('statut_jury', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(StatutJury::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new StatutJury([]);
        $this->assertEquals('id_statut', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(StatutJury::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new StatutJury([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_statut', $fillable);
        $this->assertContains('description', $fillable);
    }
}
