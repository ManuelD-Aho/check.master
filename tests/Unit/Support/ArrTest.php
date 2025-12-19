<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\Arr;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour Arr
 */
class ArrTest extends TestCase
{
    /**
     * @test
     */
    public function testArrClassExists(): void
    {
        $this->assertTrue(class_exists(Arr::class));
    }
}
