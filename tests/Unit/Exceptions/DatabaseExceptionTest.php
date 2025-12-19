<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\DatabaseException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour DatabaseException
 */
class DatabaseExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new DatabaseException('Erreur DB');
        $this->assertEquals(500, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new DatabaseException();
        $this->assertEquals('DATABASE_ERROR', $exception->getErrorCode());
    }
}
