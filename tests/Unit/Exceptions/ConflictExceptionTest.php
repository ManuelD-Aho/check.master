<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\ConflictException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ConflictException
 */
class ConflictExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new ConflictException('Conflit détecté');
        $this->assertEquals(409, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new ConflictException();
        $this->assertEquals('CONFLICT', $exception->getErrorCode());
    }
}
