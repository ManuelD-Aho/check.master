<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour les fonctions helpers globales
 */
class HelpersTest extends TestCase
{
    /**
     * @test
     */
    public function testHelpersFileExists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 3) . '/src/Support/helpers.php');
    }
}
