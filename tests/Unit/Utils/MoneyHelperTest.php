<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Utils\MoneyHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour MoneyHelper
 */
class MoneyHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testMoneyHelperInstantiation(): void
    {
        $this->assertTrue(class_exists(MoneyHelper::class));
    }
}
