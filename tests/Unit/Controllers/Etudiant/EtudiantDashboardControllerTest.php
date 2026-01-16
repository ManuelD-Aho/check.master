<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Etudiant;

use PHPUnit\Framework\TestCase;
use App\Controllers\Etudiant\DashboardController;

class EtudiantDashboardControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(DashboardController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(DashboardController::class, 'index'));
    }

    public function testMethodStatsExiste(): void
    {
        $this->assertTrue(method_exists(DashboardController::class, 'stats'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(DashboardController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
