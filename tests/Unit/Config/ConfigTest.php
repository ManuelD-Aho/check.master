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
        $this->assertFileExists(dirname(__DIR__, 3) . '/app/config/security.php');
    }

    /**
     * @test
     */
    public function testSessionConfigExists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 3) . '/app/config/session.php');
    }

    /**
     * @test
     */
    public function testEmailConfigExists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 3) . '/app/config/email.php');
    }

    /**
     * @test
     */
    public function testWorkflowConfigExists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 3) . '/app/config/workflow.php');
    }
}
