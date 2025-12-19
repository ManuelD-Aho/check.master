<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\VerrouillageMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour VerrouillageMiddleware
 */
class VerrouillageMiddlewareTest extends TestCase
{
    private VerrouillageMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new VerrouillageMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(VerrouillageMiddleware::class, $this->middleware);
    }
}
