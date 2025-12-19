<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Utils\PdfHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour PdfHelper
 */
class PdfHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testPdfHelperInstantiation(): void
    {
        $this->assertTrue(class_exists(PdfHelper::class));
    }
}
