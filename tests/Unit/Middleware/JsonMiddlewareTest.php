<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\JsonMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour JsonMiddleware
 */
class JsonMiddlewareTest extends TestCase
{
    private JsonMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new JsonMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(JsonMiddleware::class, $this->middleware);
    }
}
