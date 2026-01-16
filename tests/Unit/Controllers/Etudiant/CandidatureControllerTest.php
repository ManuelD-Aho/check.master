<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Etudiant;

use PHPUnit\Framework\TestCase;
use App\Controllers\Etudiant\CandidatureController;

class CandidatureControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(CandidatureController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(CandidatureController::class, 'index'));
    }

    public function testMethodShowExiste(): void
    {
        $this->assertTrue(method_exists(CandidatureController::class, 'show'));
    }

    public function testMethodStoreExiste(): void
    {
        $this->assertTrue(method_exists(CandidatureController::class, 'store'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(CandidatureController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
