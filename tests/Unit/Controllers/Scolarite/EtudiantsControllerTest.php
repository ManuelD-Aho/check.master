<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Scolarite;

use PHPUnit\Framework\TestCase;
use App\Controllers\Scolarite\EtudiantsController;

class EtudiantsControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(EtudiantsController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(EtudiantsController::class, 'index'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(EtudiantsController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testConstructeurExiste(): void
    {
        $reflection = new \ReflectionClass(EtudiantsController::class);
        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
