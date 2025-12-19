<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\CacheFactory;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour CacheFactory
 */
class CacheFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testCacheFactoryClassExists(): void
    {
        $this->assertTrue(class_exists(CacheFactory::class));
    }
}
