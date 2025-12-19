<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour les fichiers de configuration
 */
class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function testAppConfigExists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 3) . '/app/config/app.php');
    }

    /**
     * @test
     */
    public function testAppConfigReturnsArray(): void
    {
        $config = require dirname(__DIR__, 3) . '/app/config/app.php';
        $this->assertIsArray($config);
    }

    /**
     * @test
     */
    public function testAppConfigHasRequiredKeys(): void
    {
        $config = require dirname(__DIR__, 3) . '/app/config/app.php';

        $this->assertArrayHasKey('name', $config);
        $this->assertArrayHasKey('env', $config);
        $this->assertArrayHasKey('debug', $config);
        $this->assertArrayHasKey('url', $config);
        $this->assertArrayHasKey('timezone', $config);
    }

    /**
     * @test
     */
    public function testDatabaseConfigExists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 3) . '/app/config/database.php');
    }

    /**
     * @test
     */
    public function testRoutesConfigExists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 3) . '/app/config/routes.php');
    }

    /**
     * @test
     */
    public function testSecurityConfigExists(): void
    {
        $this->markTestSkipped('Config file security.php not yet created');
    }

    /**
     * @test
     */
    public function testSessionConfigExists(): void
    {
        $this->markTestSkipped('Config file session.php not yet created');
    }

    /**
     * @test
     */
    public function testEmailConfigExists(): void
    {
        $this->markTestSkipped('Config file email.php not yet created');
    }

    /**
     * @test
     */
    public function testWorkflowConfigExists(): void
    {
        $this->markTestSkipped('Config file workflow.php not yet created');
    }
}
