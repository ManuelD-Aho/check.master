<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\SecurityHeadersMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour SecurityHeadersMiddleware
 */
class SecurityHeadersMiddlewareTest extends TestCase
{
    private SecurityHeadersMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new SecurityHeadersMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(SecurityHeadersMiddleware::class, $this->middleware);
    }
}
