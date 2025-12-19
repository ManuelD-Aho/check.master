<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Src\Exceptions\AppException;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour AppException
 */
class AppExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructeurDefaut(): void
    {
        $exception = new AppException();
        $this->assertEquals(500, $exception->getHttpCode());
        $this->assertEquals('INTERNAL_ERROR', $exception->getErrorCode());
    }

    /**
     * @test
     */
    public function testConstructeurAvecParametres(): void
    {
        $exception = new AppException('Message personnalisé', 400, 'CUSTOM_ERROR', ['key' => 'value']);
        $this->assertEquals('Message personnalisé', $exception->getMessage());
        $this->assertEquals(400, $exception->getHttpCode());
        $this->assertEquals('CUSTOM_ERROR', $exception->getErrorCode());
        $this->assertEquals(['key' => 'value'], $exception->getDetails());
    }

    /**
     * @test
     */
    public function testAddDetail(): void
    {
        $exception = new AppException();
        $exception->addDetail('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $exception->getDetails());
    }

    /**
     * @test
     */
    public function testToArray(): void
    {
        $exception = new AppException('Test', 400, 'TEST_ERROR', ['field' => 'error']);
        $array = $exception->toArray();
        $this->assertArrayHasKey('error', $array);
        $this->assertArrayHasKey('code', $array);
        $this->assertArrayHasKey('message', $array);
        $this->assertArrayHasKey('details', $array);
        $this->assertTrue($array['error']);
        $this->assertEquals('TEST_ERROR', $array['code']);
    }

    /**
     * @test
     */
    public function testToJson(): void
    {
        $exception = new AppException('Test', 400);
        $json = $exception->toJson();
        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertEquals('Test', $decoded['message']);
    }

    /**
     * @test
     */
    public function testFromMessage(): void
    {
        $exception = AppException::fromMessage('Error message', 404);
        $this->assertEquals('Error message', $exception->getMessage());
        $this->assertEquals(404, $exception->getHttpCode());
    }
}
