<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Finance;

use PHPUnit\Framework\TestCase;
use App\Controllers\Finance\PaiementsController;

class PaiementsControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(PaiementsController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(PaiementsController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(PaiementsController::class, 'list'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(PaiementsController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testConstructeurExiste(): void
    {
        $reflection = new \ReflectionClass(PaiementsController::class);
        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
