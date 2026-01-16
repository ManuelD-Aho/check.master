<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\EscaladeAction;

class EscaladeActionTest extends TestCase
{
    public function testConstantesTypesActionsDefinies(): void
    {
        $this->assertEquals('prise_en_charge', EscaladeAction::TYPE_PRISE_EN_CHARGE);
        $this->assertEquals('commentaire', EscaladeAction::TYPE_COMMENTAIRE);
        $this->assertEquals('escalade_superieure', EscaladeAction::TYPE_ESCALADE_SUPERIEURE);
        $this->assertEquals('resolution', EscaladeAction::TYPE_RESOLUTION);
        $this->assertEquals('fermeture', EscaladeAction::TYPE_FERMETURE);
        $this->assertEquals('reassignation', EscaladeAction::TYPE_REASSIGNATION);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(EscaladeAction::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new EscaladeAction([]);
        $this->assertEquals('escalades_actions', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(EscaladeAction::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new EscaladeAction([]);
        $this->assertEquals('id_action', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(EscaladeAction::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new EscaladeAction([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('escalade_id', $fillable);
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('type_action', $fillable);
        $this->assertContains('description', $fillable);
    }
}
