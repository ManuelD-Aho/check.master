<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Core\ServiceHashids;

/**
 * Tests unitaires pour ServiceHashids
 * 
 * @covers \App\Services\Core\ServiceHashids
 */
class ServiceHashidsTest extends TestCase
{
    protected function setUp(): void
    {
        ServiceHashids::init();
    }

    /**
     * @test
     * Test de l'initialisation
     */
    public function testInit(): void
    {
        ServiceHashids::init();
        $this->assertTrue(true);
    }

    /**
     * @test
     * Test de l'encodage d'un ID
     */
    public function testEncode(): void
    {
        $hash = ServiceHashids::encode(1);

        $this->assertIsString($hash);
        $this->assertGreaterThanOrEqual(8, strlen($hash)); // MIN_LENGTH = 8
    }

    /**
     * @test
     * Test de l'encodage de plusieurs IDs
     */
    public function testEncodeMultiple(): void
    {
        $hash = ServiceHashids::encodeMultiple(1, 2, 3);

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    /**
     * @test
     * Test du décodage d'un hash valide
     */
    public function testDecode(): void
    {
        $id = 42;
        $hash = ServiceHashids::encode($id);
        $decoded = ServiceHashids::decode($hash);

        $this->assertEquals($id, $decoded);
    }

    /**
     * @test
     * Test du décodage d'un hash invalide
     */
    public function testDecodeInvalide(): void
    {
        $decoded = ServiceHashids::decode('invalid_hash_!!!');

        $this->assertNull($decoded);
    }

    /**
     * @test
     * Test du décodage de plusieurs IDs
     */
    public function testDecodeMultiple(): void
    {
        $hash = ServiceHashids::encodeMultiple(1, 2, 3);
        $decoded = ServiceHashids::decodeMultiple($hash);

        $this->assertIsArray($decoded);
        $this->assertCount(3, $decoded);
        $this->assertEquals([1, 2, 3], $decoded);
    }

    /**
     * @test
     * Test de la validation d'un hash
     */
    public function testIsValid(): void
    {
        $hash = ServiceHashids::encode(1);

        $this->assertTrue(ServiceHashids::isValid($hash));
        $this->assertFalse(ServiceHashids::isValid('invalid!!!'));
    }

    /**
     * @test
     * Test de l'encodage d'entité
     */
    public function testEncodeEntity(): void
    {
        $hash = ServiceHashids::encodeEntity('utilisateur', 1);

        $this->assertIsString($hash);
        $this->assertStringStartsWith('usr_', $hash);
    }

    /**
     * @test
     * Test du décodage d'entité
     */
    public function testDecodeEntity(): void
    {
        $hash = ServiceHashids::encodeEntity('utilisateur', 42);
        $decoded = ServiceHashids::decodeEntity($hash);

        $this->assertIsArray($decoded);
        $this->assertEquals('utilisateur', $decoded['type']);
        $this->assertEquals(42, $decoded['id']);
    }

    /**
     * @test
     * Test du décodage d'entité invalide
     */
    public function testDecodeEntityInvalide(): void
    {
        $decoded = ServiceHashids::decodeEntity('invalid');

        $this->assertNull($decoded);
    }

    /**
     * @test
     * Test de l'encodage pour URL
     */
    public function testForUrl(): void
    {
        $hash = ServiceHashids::forUrl(1);

        $this->assertIsString($hash);
        $this->assertEquals(ServiceHashids::encode(1), $hash);
    }

    /**
     * @test
     * Test du décodage depuis URL
     */
    public function testFromUrl(): void
    {
        $hash = ServiceHashids::forUrl(42);
        $id = ServiceHashids::fromUrl($hash);

        $this->assertEquals(42, $id);
    }

    /**
     * @test
     * Test de l'encodage avec contexte
     */
    public function testEncodeWithContext(): void
    {
        $hash = ServiceHashids::encodeWithContext(1, 'mon_contexte');

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    /**
     * @test
     * Test du décodage avec contexte valide
     */
    public function testDecodeWithContextValide(): void
    {
        $contexte = 'mon_contexte';
        $hash = ServiceHashids::encodeWithContext(42, $contexte);
        $id = ServiceHashids::decodeWithContext($hash, $contexte);

        $this->assertEquals(42, $id);
    }

    /**
     * @test
     * Test du décodage avec mauvais contexte
     */
    public function testDecodeWithContextMauvais(): void
    {
        $hash = ServiceHashids::encodeWithContext(42, 'contexte_original');
        $id = ServiceHashids::decodeWithContext($hash, 'autre_contexte');

        $this->assertNull($id);
    }

    /**
     * @test
     * Test de la génération de token
     */
    public function testGenerateToken(): void
    {
        $token1 = ServiceHashids::generateToken();
        $token2 = ServiceHashids::generateToken();

        $this->assertIsString($token1);
        $this->assertIsString($token2);
        $this->assertNotEquals($token1, $token2);
    }

    /**
     * @test
     * Test des préfixes d'entités
     */
    public function testPrefixesEntites(): void
    {
        $entites = [
            'utilisateur' => 'usr',
            'etudiant' => 'etu',
            'enseignant' => 'ens',
            'dossier' => 'dos',
            'rapport' => 'rap',
            'soutenance' => 'sou',
            'paiement' => 'pai',
        ];

        foreach ($entites as $type => $prefixeAttendu) {
            $hash = ServiceHashids::encodeEntity($type, 1);
            $this->assertStringStartsWith($prefixeAttendu . '_', $hash);
        }
    }

    /**
     * @test
     * Test de la longueur minimale
     */
    public function testLongueurMinimale(): void
    {
        // Tous les hashes doivent avoir au moins 8 caractères
        for ($i = 1; $i <= 100; $i++) {
            $hash = ServiceHashids::encode($i);
            $this->assertGreaterThanOrEqual(8, strlen($hash));
        }
    }

    /**
     * @test
     * Test de l'alphabet utilisé
     */
    public function testAlphabet(): void
    {
        $hash = ServiceHashids::encode(12345);

        // Vérifie que seuls les caractères alphanumériques sont présents
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $hash);
    }
}
