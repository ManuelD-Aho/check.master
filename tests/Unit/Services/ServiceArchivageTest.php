<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Archive\ServiceArchivage;

/**
 * Tests unitaires exhaustifs pour ServiceArchivage
 * 
 * @covers \App\Services\Archive\ServiceArchivage
 */
class ServiceArchivageTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceArchivage::class));
    }

    // =========================================================================
    // Tests de la méthode archiver()
    // =========================================================================

    /**
     * @test
     * La méthode archiver existe avec les bons paramètres
     */
    public function testArchiverMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceArchivage::class, 'archiver'));

        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'archiver');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('documentId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $this->assertTrue($params[1]->allowsNull());
    }

    /**
     * @test
     * La méthode archiver est statique
     */
    public function testArchiverEstStatique(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'archiver');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * @test
     * La méthode archiver retourne Archive
     */
    public function testArchiverReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'archiver');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('App\Models\Archive', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode verifierIntegrite()
    // =========================================================================

    /**
     * @test
     * La méthode verifierIntegrite existe avec les bons paramètres
     */
    public function testVerifierIntegriteMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceArchivage::class, 'verifierIntegrite'));

        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'verifierIntegrite');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('archiveId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
    }

    /**
     * @test
     * La méthode verifierIntegrite retourne bool
     */
    public function testVerifierIntegriteReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'verifierIntegrite');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode verrouiller()
    // =========================================================================

    /**
     * @test
     * La méthode verrouiller existe avec les bons paramètres
     */
    public function testVerrouillerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceArchivage::class, 'verrouiller'));

        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'verrouiller');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('archiveId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
    }

    /**
     * @test
     * La méthode verrouiller retourne bool
     */
    public function testVerrouillerReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'verrouiller');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode deverrouiller()
    // =========================================================================

    /**
     * @test
     * La méthode deverrouiller existe avec les bons paramètres
     */
    public function testDeverrouillerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceArchivage::class, 'deverrouiller'));

        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'deverrouiller');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('archiveId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $this->assertEquals('motif', $params[2]->getName());
    }

    /**
     * @test
     * Le motif est obligatoire pour le déverrouillage
     */
    public function testDeverrouillerMotifObligatoire(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'deverrouiller');
        $params = $reflection->getParameters();

        $motifParam = $params[2];
        $this->assertFalse($motifParam->isOptional());
        $this->assertEquals('string', $motifParam->getType()->getName());
    }

    // =========================================================================
    // Tests de la méthode getArchivesAVerifier()
    // =========================================================================

    /**
     * @test
     * La méthode getArchivesAVerifier existe
     */
    public function testGetArchivesAVerifierMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceArchivage::class, 'getArchivesAVerifier'));

        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'getArchivesAVerifier');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('joursDepuisVerification', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertEquals(30, $params[0]->getDefaultValue());
    }

    /**
     * @test
     * La méthode getArchivesAVerifier retourne array
     */
    public function testGetArchivesAVerifierReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'getArchivesAVerifier');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode verifierToutesArchives()
    // =========================================================================

    /**
     * @test
     * La méthode verifierToutesArchives existe
     */
    public function testVerifierToutesArchivesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceArchivage::class, 'verifierToutesArchives'));
    }

    /**
     * @test
     * La méthode verifierToutesArchives n'a pas de paramètres
     */
    public function testVerifierToutesArchivesSansParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'verifierToutesArchives');
        $this->assertCount(0, $reflection->getParameters());
    }

    /**
     * @test
     * La méthode verifierToutesArchives retourne array
     */
    public function testVerifierToutesArchivesReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'verifierToutesArchives');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode getStatistiques()
    // =========================================================================

    /**
     * @test
     * La méthode getStatistiques existe
     */
    public function testGetStatistiquesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceArchivage::class, 'getStatistiques'));
    }

    /**
     * @test
     * La méthode getStatistiques retourne array
     */
    public function testGetStatistiquesReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceArchivage::class, 'getStatistiques');
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
            'archiver',
            'verifierIntegrite',
            'verrouiller',
            'deverrouiller',
            'getArchivesAVerifier',
            'verifierToutesArchives',
            'getStatistiques',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceArchivage::class, $method);
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
     * La classe n'a pas de constructeur public
     */
    public function testPasDeConstructeurPublicNecessaire(): void
    {
        $reflection = new \ReflectionClass(ServiceArchivage::class);
        $constructor = $reflection->getConstructor();

        // Un service statique n'a généralement pas de constructeur ou un constructeur privé
        if ($constructor !== null) {
            $this->assertTrue($constructor->isPrivate() || $constructor->isProtected());
        } else {
            $this->assertNull($constructor);
        }
    }
}
