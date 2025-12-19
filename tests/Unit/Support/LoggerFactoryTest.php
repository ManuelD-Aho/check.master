<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\LoggerFactory;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour LoggerFactory
 */
class LoggerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testLoggerFactoryClassExists(): void
    {
        $this->assertTrue(class_exists(LoggerFactory::class));
    }
}
