<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Workflow;

use PHPUnit\Framework\TestCase;
use App\Controllers\Workflow\WorkflowController;

class WorkflowControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(WorkflowController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(WorkflowController::class, 'index'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(WorkflowController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testConstructeurExiste(): void
    {
        $reflection = new \ReflectionClass(WorkflowController::class);
        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
