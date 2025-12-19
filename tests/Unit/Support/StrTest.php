<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour Str
 */
class StrTest extends TestCase
{
    /**
     * @test
     */
    public function testCamel(): void
    {
        $this->assertEquals('helloWorld', Str::camel('hello_world'));
        $this->assertEquals('helloWorld', Str::camel('hello-world'));
    }

    /**
     * @test
     */
    public function testStudly(): void
    {
        $this->assertEquals('HelloWorld', Str::studly('hello_world'));
    }

    /**
     * @test
     */
    public function testSnake(): void
    {
        $this->assertEquals('hello_world', Str::snake('helloWorld'));
        $this->assertEquals('hello_world', Str::snake('HelloWorld'));
    }

    /**
     * @test
     */
    public function testKebab(): void
    {
        $this->assertEquals('hello-world', Str::kebab('helloWorld'));
    }

    /**
     * @test
     */
    public function testSlug(): void
    {
        $this->assertEquals('hello-world', Str::slug('Hello World'));
        $this->assertEquals('bonjour-le-monde', Str::slug('Bonjour le monde!'));
    }

    /**
     * @test
     */
    public function testLimit(): void
    {
        $result = Str::limit('This is a long text', 10);
        $this->assertEquals('This is a...', $result);
    }

    /**
     * @test
     */
    public function testStartsWith(): void
    {
        $this->assertTrue(Str::startsWith('Hello World', 'Hello'));
        $this->assertFalse(Str::startsWith('Hello World', 'World'));
    }

    /**
     * @test
     */
    public function testEndsWith(): void
    {
        $this->assertTrue(Str::endsWith('Hello World', 'World'));
        $this->assertFalse(Str::endsWith('Hello World', 'Hello'));
    }

    /**
     * @test
     */
    public function testContains(): void
    {
        $this->assertTrue(Str::contains('Hello World', 'lo Wo'));
        $this->assertFalse(Str::contains('Hello World', 'xyz'));
    }

    /**
     * @test
     */
    public function testRandom(): void
    {
        $random = Str::random(16);
        $this->assertEquals(16, strlen($random));
    }

    /**
     * @test
     */
    public function testUuid(): void
    {
        $uuid = Str::uuid();
        $this->assertTrue(Str::isUuid($uuid));
    }

    /**
     * @test
     */
    public function testIsUuid(): void
    {
        $this->assertTrue(Str::isUuid('550e8400-e29b-41d4-a716-446655440000'));
        $this->assertFalse(Str::isUuid('not-a-uuid'));
    }

    /**
     * @test
     */
    public function testUpper(): void
    {
        $this->assertEquals('HELLO', Str::upper('hello'));
    }

    /**
     * @test
     */
    public function testLower(): void
    {
        $this->assertEquals('hello', Str::lower('HELLO'));
    }

    /**
     * @test
     */
    public function testLength(): void
    {
        $this->assertEquals(5, Str::length('hello'));
    }
}
