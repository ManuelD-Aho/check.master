<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\MaintenanceMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour MaintenanceMiddleware
 */
class MaintenanceMiddlewareTest extends TestCase
{
    private MaintenanceMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new MaintenanceMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(MaintenanceMiddleware::class, $this->middleware);
    }
}
