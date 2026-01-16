<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Commission;

use PHPUnit\Framework\TestCase;
use App\Controllers\Commission\EvaluationsController;

class EvaluationsControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(EvaluationsController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(EvaluationsController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(EvaluationsController::class, 'list'));
    }

    public function testMethodEvaluerExiste(): void
    {
        $this->assertTrue(method_exists(EvaluationsController::class, 'evaluer'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(EvaluationsController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
