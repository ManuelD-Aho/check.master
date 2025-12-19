<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\ThrottleMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ThrottleMiddleware
 */
class ThrottleMiddlewareTest extends TestCase
{
    private ThrottleMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new ThrottleMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(ThrottleMiddleware::class, $this->middleware);
    }
}
