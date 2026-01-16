<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\MaintenanceMode;

class MaintenanceModeTest extends TestCase
{
    public function testConstantesMessagesDefinies(): void
    {
        $this->assertNotEmpty(MaintenanceMode::MESSAGE_DEFAUT);
        $this->assertNotEmpty(MaintenanceMode::MESSAGE_MISE_A_JOUR);
    }

    public function testMethodeEstActifExiste(): void
    {
        $this->assertTrue(method_exists(MaintenanceMode::class, 'estActif'));
        
        $reflection = new \ReflectionMethod(MaintenanceMode::class, 'estActif');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(MaintenanceMode::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new MaintenanceMode([]);
        $this->assertEquals('maintenance_mode', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(MaintenanceMode::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new MaintenanceMode([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('actif', $fillable);
        $this->assertContains('message', $fillable);
        $this->assertContains('debut_maintenance', $fillable);
        $this->assertContains('fin_maintenance', $fillable);
    }
}
