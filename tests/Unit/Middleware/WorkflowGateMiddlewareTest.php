<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\WorkflowGateMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour WorkflowGateMiddleware
 * 
 * Ce middleware bloque /etudiant/rapport/* si état != candidature_validée
 */
class WorkflowGateMiddlewareTest extends TestCase
{
    private WorkflowGateMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new WorkflowGateMiddleware('test_tab', ['candidature_validee']);
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(WorkflowGateMiddleware::class, $this->middleware);
    }
}
