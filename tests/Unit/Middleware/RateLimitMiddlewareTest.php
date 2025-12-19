<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\RateLimitMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour RateLimitMiddleware
 */
class RateLimitMiddlewareTest extends TestCase
{
    private RateLimitMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new RateLimitMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(RateLimitMiddleware::class, $this->middleware);
    }
}
