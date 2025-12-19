<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\CsrfMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour CsrfMiddleware
 */
class CsrfMiddlewareTest extends TestCase
{
    private CsrfMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new CsrfMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(CsrfMiddleware::class, $this->middleware);
    }
}
