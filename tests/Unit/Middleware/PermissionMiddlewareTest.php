<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\PermissionMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour PermissionMiddleware
 */
class PermissionMiddlewareTest extends TestCase
{
    private PermissionMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new PermissionMiddleware('test_resource', 'lire');
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(PermissionMiddleware::class, $this->middleware);
    }
}
