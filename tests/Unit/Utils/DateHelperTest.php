<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Utils\DateHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour DateHelper
 */
class DateHelperTest extends TestCase
{
    /**
     * @test
     */
    public function testFormatDateValide(): void
    {
        $result = DateHelper::format('2025-12-15');
        $this->assertEquals('15/12/2025', $result);
    }

    /**
     * @test
     */
    public function testFormatDateVide(): void
    {
        $result = DateHelper::format('');
        $this->assertEquals('', $result);
    }

    /**
     * @test
     */
    public function testFormatDateNull(): void
    {
        $result = DateHelper::format(null);
        $this->assertEquals('', $result);
    }

    /**
     * @test
     */
    public function testFormatAvecFormatPersonnalise(): void
    {
        $result = DateHelper::format('2025-12-15', 'd-m-Y');
        $this->assertEquals('15-12-2025', $result);
    }

    /**
     * @test
     */
    public function testFormatFrancais(): void
    {
        $result = DateHelper::formatFr('2025-12-15');
        $this->assertStringContainsString('15', $result);
        $this->assertStringContainsString('décembre', $result);
        $this->assertStringContainsString('2025', $result);
    }

    /**
     * @test
     */
    public function testRelative(): void
    {
        $result = DateHelper::relative(date('Y-m-d H:i:s'));
        $this->assertStringContainsString('instant', $result);
    }

    /**
     * @test
     */
    public function testDiffJours(): void
    {
        $jours = DateHelper::diffJours('2025-01-01', '2025-01-10');
        $this->assertEquals(9, $jours);
    }

    /**
     * @test
     */
    public function testEstPasse(): void
    {
        $this->assertTrue(DateHelper::estPasse('2020-01-01'));
        $this->assertFalse(DateHelper::estPasse('2099-01-01'));
    }

    /**
     * @test
     */
    public function testEstFutur(): void
    {
        $this->assertTrue(DateHelper::estFutur('2099-01-01'));
        $this->assertFalse(DateHelper::estFutur('2020-01-01'));
    }

    /**
     * @test
     */
    public function testEstAujourdhui(): void
    {
        $this->assertTrue(DateHelper::estAujourdhui(date('Y-m-d')));
        $this->assertFalse(DateHelper::estAujourdhui('2020-01-01'));
    }

    /**
     * @test
     */
    public function testDebutJournee(): void
    {
        $result = DateHelper::debutJournee('2025-12-15');
        $this->assertEquals('00:00:00', $result->format('H:i:s'));
    }

    /**
     * @test
     */
    public function testFinJournee(): void
    {
        $result = DateHelper::finJournee('2025-12-15');
        $this->assertEquals('23:59:59', $result->format('H:i:s'));
    }

    /**
     * @test
     */
    public function testAjouterJours(): void
    {
        $result = DateHelper::ajouterJours('2025-01-01 00:00:00', 5);
        $this->assertStringContainsString('2025-01-06', $result);
    }

    /**
     * @test
     */
    public function testAnneeAcademiqueAvantSeptembre(): void
    {
        $result = DateHelper::anneeAcademique('2025-03-15');
        $this->assertEquals('2024-2025', $result);
    }

    /**
     * @test
     */
    public function testAnneeAcademiqueApresSeptembre(): void
    {
        $result = DateHelper::anneeAcademique('2025-09-15');
        $this->assertEquals('2025-2026', $result);
    }
}
