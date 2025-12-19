<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\LoggingMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour LoggingMiddleware
 */
class LoggingMiddlewareTest extends TestCase
{
    private LoggingMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new LoggingMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(LoggingMiddleware::class, $this->middleware);
    }
}
