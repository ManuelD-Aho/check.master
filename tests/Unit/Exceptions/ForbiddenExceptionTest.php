<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\ForbiddenException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ForbiddenException
 */
class ForbiddenExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new ForbiddenException('Accès interdit');
        $this->assertEquals(403, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new ForbiddenException();
        $this->assertEquals('FORBIDDEN', $exception->getErrorCode());
    }
}
