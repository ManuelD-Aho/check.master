<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ValidationException
 */
class ValidationExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new ValidationException(['field' => 'Validation échouée']);
        $this->assertEquals(422, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new ValidationException([]);
        $this->assertEquals('VALIDATION_ERROR', $exception->getErrorCode());
    }
}
