<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\DashboardController;

class DashboardControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(DashboardController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(DashboardController::class, 'index'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(DashboardController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('Src\\Http\\Response', $returnType->getName());
    }

    public function testIndexEstPublique(): void
    {
        $reflection = new \ReflectionMethod(DashboardController::class, 'index');
        $this->assertTrue($reflection->isPublic());
    }
}
