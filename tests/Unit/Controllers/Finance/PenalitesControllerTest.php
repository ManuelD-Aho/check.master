<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Finance;

use PHPUnit\Framework\TestCase;
use App\Controllers\Finance\PenalitesController;

class PenalitesControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(PenalitesController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(PenalitesController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(PenalitesController::class, 'list'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(PenalitesController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testConstructeurExiste(): void
    {
        $reflection = new \ReflectionClass(PenalitesController::class);
        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
