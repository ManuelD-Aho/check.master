<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\TooManyRequestsException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour TooManyRequestsException
 */
class TooManyRequestsExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new TooManyRequestsException(60, 'Trop de requêtes');
        $this->assertEquals(429, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new TooManyRequestsException();
        $this->assertEquals('TOO_MANY_REQUESTS', $exception->getErrorCode());
    }
}
