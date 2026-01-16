<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Scolarite;

use PHPUnit\Framework\TestCase;
use App\Controllers\Scolarite\CandidaturesController;

class CandidaturesControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(CandidaturesController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(CandidaturesController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(CandidaturesController::class, 'list'));
    }

    public function testMethodValiderExiste(): void
    {
        $this->assertTrue(method_exists(CandidaturesController::class, 'valider'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(CandidaturesController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
