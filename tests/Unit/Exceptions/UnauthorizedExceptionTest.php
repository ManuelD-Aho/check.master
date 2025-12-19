<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\UnauthorizedException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour UnauthorizedException
 */
class UnauthorizedExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new UnauthorizedException('Non autorisé');
        $this->assertEquals(401, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new UnauthorizedException();
        $this->assertEquals('UNAUTHORIZED', $exception->getErrorCode());
    }
}
