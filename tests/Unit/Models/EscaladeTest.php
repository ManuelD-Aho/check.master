<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Escalade;

class EscaladeTest extends TestCase
{
    public function testConstantesTypesDefinies(): void
    {
        $this->assertEquals('commission_blocage', Escalade::TYPE_COMMISSION_BLOCAGE);
        $this->assertEquals('delai_depasse', Escalade::TYPE_DELAI_DEPASSE);
        $this->assertEquals('avis_absent', Escalade::TYPE_AVIS_ABSENT);
        $this->assertEquals('jury_incomplet', Escalade::TYPE_JURY_INCOMPLET);
        $this->assertEquals('reclamation', Escalade::TYPE_RECLAMATION);
        $this->assertEquals('autre', Escalade::TYPE_AUTRE);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Escalade::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Escalade([]);
        $this->assertEquals('escalades', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Escalade::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Escalade([]);
        $this->assertEquals('id_escalade', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Escalade::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Escalade([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('dossier_id', $fillable);
        $this->assertContains('type_escalade', $fillable);
        $this->assertContains('niveau_escalade', $fillable);
        $this->assertContains('statut', $fillable);
    }
}
