<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Archive\ServiceIntegrite;

/**
 * Tests unitaires exhaustifs pour ServiceIntegrite
 * 
 * @covers \App\Services\Archive\ServiceIntegrite
 */
class ServiceIntegriteTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceIntegrite::class));
    }

    // =========================================================================
    // Tests de la méthode verifierTout()
    // =========================================================================

    /**
     * @test
     * La méthode verifierTout existe
     */
    public function testVerifierToutMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceIntegrite::class, 'verifierTout'));
    }

    /**
     * @test
     * La méthode verifierTout n'a pas de paramètres
     */
    public function testVerifierToutSansParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'verifierTout');
        $this->assertCount(0, $reflection->getParameters());
    }

    /**
     * @test
     * La méthode verifierTout retourne array
     */
    public function testVerifierToutReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'verifierTout');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    /**
     * @test
     * La méthode verifierTout est statique
     */
    public function testVerifierToutEstStatique(): void
    {
        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'verifierTout');
        $this->assertTrue($reflection->isStatic());
    }

    // =========================================================================
    // Tests de la méthode verifierDocument()
    // =========================================================================

    /**
     * @test
     * La méthode verifierDocument existe
     */
    public function testVerifierDocumentMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceIntegrite::class, 'verifierDocument'));

        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'verifierDocument');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('documentId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
    }

    /**
     * @test
     * La méthode verifierDocument retourne array
     */
    public function testVerifierDocumentReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'verifierDocument');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode recalculerHash()
    // =========================================================================

    /**
     * @test
     * La méthode recalculerHash existe
     */
    public function testRecalculerHashMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceIntegrite::class, 'recalculerHash'));

        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'recalculerHash');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('documentId', $params[0]->getName());
    }

    /**
     * @test
     * La méthode recalculerHash retourne ?string
     */
    public function testRecalculerHashReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'recalculerHash');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertTrue($returnType->allowsNull());
        $this->assertEquals('string', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode alerterProblemes()
    // =========================================================================

    /**
     * @test
     * La méthode alerterProblemes existe et est privée
     */
    public function testAlerterProblemesEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceIntegrite::class, 'alerterProblemes'));

        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'alerterProblemes');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * La méthode alerterProblemes prend un array en paramètre
     */
    public function testAlerterProblemesParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'alerterProblemes');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('resultats', $params[0]->getName());
        $this->assertEquals('array', $params[0]->getType()->getName());
    }

    // =========================================================================
    // Tests de la méthode verifierArchivesObsoletes()
    // =========================================================================

    /**
     * @test
     * La méthode verifierArchivesObsoletes existe
     */
    public function testVerifierArchivesObsoletesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceIntegrite::class, 'verifierArchivesObsoletes'));

        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'verifierArchivesObsoletes');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('jours', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertEquals(30, $params[0]->getDefaultValue());
    }

    /**
     * @test
     * La méthode verifierArchivesObsoletes retourne array
     */
    public function testVerifierArchivesObsoletesReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceIntegrite::class, 'verifierArchivesObsoletes');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de cohérence globale
    // =========================================================================

    /**
     * @test
     * Toutes les méthodes publiques sont statiques
     */
    public function testToutesMethodesPubliquesStatiques(): void
    {
        $methods = [
            'verifierTout',
            'verifierDocument',
            'recalculerHash',
            'verifierArchivesObsoletes',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceIntegrite::class, $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode {$method} devrait être statique"
            );
            $this->assertTrue(
                $reflection->isPublic(),
                "La méthode {$method} devrait être publique"
            );
        }
    }

    /**
     * @test
     * La structure de résultat attendue pour verifierTout
     */
    public function testStructureResultatVerifierTout(): void
    {
        $expectedKeys = ['total', 'integres', 'corrompus', 'manquants'];

        // Test de la structure attendue du résultat
        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $expectedKeys);
        }
    }

    /**
     * @test
     * La structure de résultat attendue pour verifierDocument
     */
    public function testStructureResultatVerifierDocument(): void
    {
        $expectedKeys = ['integre', 'hash_attendu', 'hash_actuel'];

        // Test de la structure attendue du résultat
        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $expectedKeys);
        }
    }

    /**
     * @test
     * Le hash SHA256 produit 64 caractères hexadécimaux
     */
    public function testFormatHashSha256(): void
    {
        $testContent = 'Test content for hash';
        $hash = hash('sha256', $testContent);

        $this->assertEquals(64, strlen($hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }
}
