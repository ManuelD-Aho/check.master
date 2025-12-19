<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\CSRF;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour CSRF
 */
class CSRFTest extends TestCase
{
    /**
     * @test
     */
    public function testCSRFClassExists(): void
    {
        $this->assertTrue(class_exists(CSRF::class));
    }
}
