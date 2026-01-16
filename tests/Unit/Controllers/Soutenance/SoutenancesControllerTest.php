<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Soutenance;

use PHPUnit\Framework\TestCase;
use App\Controllers\Soutenance\SoutenancesController;

class SoutenancesControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(SoutenancesController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(SoutenancesController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(SoutenancesController::class, 'list'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(SoutenancesController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testConstructeurExiste(): void
    {
        $reflection = new \ReflectionClass(SoutenancesController::class);
        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
