<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use App\Controllers\Admin\ParametresController;

class ParametresControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(ParametresController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(ParametresController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(ParametresController::class, 'list'));
    }

    public function testMethodGetExiste(): void
    {
        $this->assertTrue(method_exists(ParametresController::class, 'get'));
    }

    public function testMethodUpdateExiste(): void
    {
        $this->assertTrue(method_exists(ParametresController::class, 'update'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(ParametresController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
