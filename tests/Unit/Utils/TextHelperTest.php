<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Utils\TextHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour TextHelper
 */
class TextHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testTextHelperInstantiation(): void
    {
        $this->assertTrue(class_exists(TextHelper::class));
    }
}
