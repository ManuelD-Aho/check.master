<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour NotFoundException
 */
class NotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testHttpCode(): void
    {
        $exception = new NotFoundException('Ressource introuvable');
        $this->assertEquals(404, $exception->getHttpCode());
    }

    /**
     * @test
     */
    public function testErrorCode(): void
    {
        $exception = new NotFoundException();
        $this->assertEquals('NOT_FOUND', $exception->getErrorCode());
    }
}
