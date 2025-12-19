<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Utils\JsonHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour JsonHelper
 */
class JsonHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testJsonHelperInstantiation(): void
    {
        $this->assertTrue(class_exists(JsonHelper::class));
    }
}
