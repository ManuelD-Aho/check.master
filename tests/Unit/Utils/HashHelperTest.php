<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Utils\HashHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour HashHelper
 */
class HashHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testEncodeIdRetourneString(): void
    {
        $hash = HashHelper::encodeId(123);
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    /**
     * @test
     */
    public function testDecodeIdRetourneIdOriginal(): void
    {
        $hash = HashHelper::encodeId(123);
        $decoded = HashHelper::decodeId($hash);
        $this->assertEquals(123, $decoded);
    }

    /**
     * @test
     */
    public function testDecodeIdInvalideRetourneNull(): void
    {
        $result = HashHelper::decodeId('invalid_hash_!!!');
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function testEncodeIdsMultiples(): void
    {
        $hash = HashHelper::encodeIds([1, 2, 3]);
        $this->assertIsString($hash);
    }

    /**
     * @test
     */
    public function testDecodeIdsMultiples(): void
    {
        $hash = HashHelper::encodeIds([1, 2, 3]);
        $ids = HashHelper::decodeIds($hash);
        $this->assertEquals([1, 2, 3], $ids);
    }

    /**
     * @test
     */
    public function testSha256(): void
    {
        $hash = HashHelper::sha256('test');
        $this->assertEquals(64, strlen($hash));
        $this->assertEquals(hash('sha256', 'test'), $hash);
    }

    /**
     * @test
     */
    public function testMd5(): void
    {
        $hash = HashHelper::md5('test');
        $this->assertEquals(32, strlen($hash));
        $this->assertEquals(md5('test'), $hash);
    }

    /**
     * @test
     */
    public function testHashPasswordRetourneArgon2id(): void
    {
        $hash = HashHelper::hashPassword('password123');
        $this->assertStringStartsWith('$argon2id$', $hash);
    }

    /**
     * @test
     */
    public function testVerifyPasswordValide(): void
    {
        $hash = HashHelper::hashPassword('password123');
        $this->assertTrue(HashHelper::verifyPassword('password123', $hash));
    }

    /**
     * @test
     */
    public function testVerifyPasswordInvalide(): void
    {
        $hash = HashHelper::hashPassword('password123');
        $this->assertFalse(HashHelper::verifyPassword('wrongpassword', $hash));
    }

    /**
     * @test
     */
    public function testRandomToken(): void
    {
        $token = HashHelper::randomToken(32);
        $this->assertEquals(32, strlen($token));
    }

    /**
     * @test
     */
    public function testRandomUrlToken(): void
    {
        $token = HashHelper::randomUrlToken(32);
        $this->assertIsString($token);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $token);
    }

    /**
     * @test
     */
    public function testHashFileNonExistant(): void
    {
        $result = HashHelper::hashFile('/non/existent/file.txt');
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function testHmac(): void
    {
        $signature = HashHelper::hmac('data', 'secret');
        $this->assertEquals(64, strlen($signature));
    }

    /**
     * @test
     */
    public function testVerifyHmacValide(): void
    {
        $signature = HashHelper::hmac('data', 'secret');
        $this->assertTrue(HashHelper::verifyHmac('data', $signature, 'secret'));
    }

    /**
     * @test
     */
    public function testVerifyHmacInvalide(): void
    {
        $signature = HashHelper::hmac('data', 'secret');
        $this->assertFalse(HashHelper::verifyHmac('tampered', $signature, 'secret'));
    }
}
