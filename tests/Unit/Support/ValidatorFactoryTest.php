<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\ValidatorFactory;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ValidatorFactory
 */
class ValidatorFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testValidatorFactoryClassExists(): void
    {
        $this->assertTrue(class_exists(ValidatorFactory::class));
    }
}
