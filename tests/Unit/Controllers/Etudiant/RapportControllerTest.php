<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Etudiant;

use PHPUnit\Framework\TestCase;
use App\Controllers\Etudiant\RapportController;

class RapportControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(RapportController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(RapportController::class, 'index'));
    }

    public function testMethodShowExiste(): void
    {
        $this->assertTrue(method_exists(RapportController::class, 'show'));
    }

    public function testMethodStoreExiste(): void
    {
        $this->assertTrue(method_exists(RapportController::class, 'store'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(RapportController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
