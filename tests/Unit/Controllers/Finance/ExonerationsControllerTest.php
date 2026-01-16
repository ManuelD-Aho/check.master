<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Finance;

use PHPUnit\Framework\TestCase;
use App\Controllers\Finance\ExonerationsController;

class ExonerationsControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(ExonerationsController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(ExonerationsController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(ExonerationsController::class, 'list'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(ExonerationsController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testConstructeurExiste(): void
    {
        $reflection = new \ReflectionClass(ExonerationsController::class);
        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
