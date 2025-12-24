<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Core\ServiceCache;

/**
 * Tests unitaires pour ServiceCache
 * 
 * @covers \App\Services\Core\ServiceCache
 */
class ServiceCacheTest extends TestCase
{
    /**
     * @test
     * Test de l'initialisation du cache
     */
    public function testInit(): void
    {
        $this->assertTrue(method_exists(ServiceCache::class, 'init'));

        // Vérifier que l'initialisation ne génère pas d'erreur
        ServiceCache::init();
        $this->assertTrue(true);
    }

    /**
     * @test
     * Test de la méthode get avec callback
     */
    public function testGetAvecCallback(): void
    {
        ServiceCache::init();

        $valeur = ServiceCache::get('test_key_' . uniqid(), function () {
            return 'valeur_test';
        });

        $this->assertEquals('valeur_test', $valeur);
    }

    /**
     * @test
     * Test de la méthode set
     */
    public function testSet(): void
    {
        ServiceCache::init();

        $result = ServiceCache::set('test_set_' . uniqid(), 'ma_valeur', 300);
        $this->assertTrue($result);
    }

    /**
     * @test
     * Test de la méthode has
     */
    public function testHas(): void
    {
        ServiceCache::init();

        $key = 'test_has_' . uniqid();
        $this->assertFalse(ServiceCache::has($key));

        ServiceCache::set($key, 'valeur');
        $this->assertTrue(ServiceCache::has($key));
    }

    /**
     * @test
     * Test de la méthode delete
     */
    public function testDelete(): void
    {
        ServiceCache::init();

        $key = 'test_delete_' . uniqid();
        ServiceCache::set($key, 'valeur');
        $this->assertTrue(ServiceCache::has($key));

        ServiceCache::delete($key);
        $this->assertFalse(ServiceCache::has($key));
    }

    /**
     * @test
     * Test de la méthode deleteMultiple
     */
    public function testDeleteMultiple(): void
    {
        ServiceCache::init();

        $keys = [
            'test_multi_' . uniqid(),
            'test_multi_' . uniqid(),
            'test_multi_' . uniqid(),
        ];

        foreach ($keys as $key) {
            ServiceCache::set($key, 'valeur');
        }

        $result = ServiceCache::deleteMultiple($keys);
        $this->assertTrue($result);
    }

    /**
     * @test
     * Test de la méthode clear
     */
    public function testClear(): void
    {
        ServiceCache::init();

        ServiceCache::set('test_clear_' . uniqid(), 'valeur');

        $result = ServiceCache::clear();
        $this->assertTrue($result);
    }

    /**
     * @test
     * Test de la méthode remember
     */
    public function testRemember(): void
    {
        ServiceCache::init();

        $key = 'test_remember_' . uniqid();
        $valeur = ServiceCache::remember($key, 'valeur_remember', 300);

        $this->assertEquals('valeur_remember', $valeur);
    }

    /**
     * @test
     * Test de la méthode invalidatePrefix
     */
    public function testInvalidatePrefix(): void
    {
        ServiceCache::init();

        // Cette méthode doit exister et s'exécuter sans erreur
        $this->assertTrue(method_exists(ServiceCache::class, 'invalidatePrefix'));
    }

    /**
     * @test
     * Test de la méthode stats (cache court)
     */
    public function testStats(): void
    {
        ServiceCache::init();

        $valeur = ServiceCache::stats('test_stats_' . uniqid(), function () {
            return ['total' => 100];
        });

        $this->assertIsArray($valeur);
        $this->assertEquals(100, $valeur['total']);
    }

    /**
     * @test
     * Test de la méthode config (cache long)
     */
    public function testConfig(): void
    {
        ServiceCache::init();

        $valeur = ServiceCache::config('test_config_' . uniqid(), function () {
            return ['setting' => 'value'];
        });

        $this->assertIsArray($valeur);
        $this->assertEquals('value', $valeur['setting']);
    }

    /**
     * @test
     * Test de la méthode permissions
     */
    public function testPermissions(): void
    {
        ServiceCache::init();

        $valeur = ServiceCache::permissions('test_perm_' . uniqid(), function () {
            return ['peut_lire' => true];
        });

        $this->assertIsArray($valeur);
        $this->assertTrue($valeur['peut_lire']);
    }

    /**
     * @test
     * Test de la méthode invalidateUserPermissions
     */
    public function testInvalidateUserPermissions(): void
    {
        ServiceCache::init();

        // Doit s'exécuter sans erreur
        ServiceCache::invalidateUserPermissions(1);
        $this->assertTrue(true);
    }

    /**
     * @test
     * Test de la méthode info
     */
    public function testInfo(): void
    {
        ServiceCache::init();

        $info = ServiceCache::info();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('directory', $info);
        $this->assertArrayHasKey('files', $info);
        $this->assertArrayHasKey('size_bytes', $info);
        $this->assertArrayHasKey('size_formatted', $info);
    }

    /**
     * @test
     * Test de la méthode formatBytes
     */
    public function testFormatBytes(): void
    {
        // La méthode est privée, on teste via info()
        $info = ServiceCache::info();

        $this->assertIsString($info['size_formatted']);
        $this->assertMatchesRegularExpression('/\d+(\.\d+)?\s+(o|Ko|Mo|Go)/', $info['size_formatted']);
    }

    /**
     * @test
     * Test du TTL par défaut
     */
    public function testTtlParDefaut(): void
    {
        // Vérifie que la constante DEFAULT_TTL existe
        $reflection = new \ReflectionClass(ServiceCache::class);

        $this->assertTrue($reflection->hasConstant('DEFAULT_TTL') || $reflection->hasProperty('DEFAULT_TTL'));
    }
}
