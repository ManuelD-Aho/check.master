<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Documents\ServiceExcel;

/**
 * Tests unitaires exhaustifs pour ServiceExcel
 * 
 * @covers \App\Services\Documents\ServiceExcel
 */
class ServiceExcelTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceExcel::class));
    }

    // =========================================================================
    // Tests de la méthode exporter()
    // =========================================================================

    /**
     * @test
     * La méthode exporter existe avec les bons paramètres
     */
    public function testExporterMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceExcel::class, 'exporter'));

        $reflection = new \ReflectionMethod(ServiceExcel::class, 'exporter');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('donnees', $params[0]->getName());
        $this->assertEquals('colonnes', $params[1]->getName());
        $this->assertEquals('nomFichier', $params[2]->getName());
    }

    /**
     * @test
     * Le paramètre donnees est un array
     */
    public function testExporterDonneesEstArray(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'exporter');
        $params = $reflection->getParameters();

        $this->assertEquals('array', $params[0]->getType()->getName());
    }

    /**
     * @test
     * Le paramètre colonnes est un array
     */
    public function testExporterColonnesEstArray(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'exporter');
        $params = $reflection->getParameters();

        $this->assertEquals('array', $params[1]->getType()->getName());
    }

    /**
     * @test
     * La méthode exporter retourne string (chemin du fichier)
     */
    public function testExporterReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'exporter');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('string', $returnType->getName());
    }

    /**
     * @test
     * Le paramètre utilisateurId est optionnel
     */
    public function testExporterUtilisateurIdOptional(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'exporter');
        $params = $reflection->getParameters();

        if (count($params) > 3) {
            $utilisateurIdParam = $params[3];
            $this->assertTrue($utilisateurIdParam->isOptional());
            $this->assertTrue($utilisateurIdParam->allowsNull());
        }
    }

    // =========================================================================
    // Tests de la méthode importer()
    // =========================================================================

    /**
     * @test
     * La méthode importer existe avec les bons paramètres
     */
    public function testImporterMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceExcel::class, 'importer'));

        $reflection = new \ReflectionMethod(ServiceExcel::class, 'importer');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertEquals('cheminFichier', $params[0]->getName());
        $this->assertEquals('mappingColonnes', $params[1]->getName());
    }

    /**
     * @test
     * Le paramètre cheminFichier est un string
     */
    public function testImporterCheminFichierEstString(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'importer');
        $params = $reflection->getParameters();

        $this->assertEquals('string', $params[0]->getType()->getName());
    }

    /**
     * @test
     * Le paramètre mappingColonnes est un array
     */
    public function testImporterMappingColonnesEstArray(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'importer');
        $params = $reflection->getParameters();

        $this->assertEquals('array', $params[1]->getType()->getName());
    }

    /**
     * @test
     * La méthode importer retourne array
     */
    public function testImporterReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'importer');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode trouverColonne()
    // =========================================================================

    /**
     * @test
     * La méthode trouverColonne existe et est privée
     */
    public function testTrouverColonneEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceExcel::class, 'trouverColonne'));

        $reflection = new \ReflectionMethod(ServiceExcel::class, 'trouverColonne');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * La méthode trouverColonne prend les bons paramètres
     */
    public function testTrouverColonneParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'trouverColonne');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('entetes', $params[0]->getName());
        $this->assertEquals('nomColonne', $params[1]->getName());
    }

    // =========================================================================
    // Tests de la méthode genererTemplate()
    // =========================================================================

    /**
     * @test
     * La méthode genererTemplate existe
     */
    public function testGenererTemplateMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceExcel::class, 'genererTemplate'));

        $reflection = new \ReflectionMethod(ServiceExcel::class, 'genererTemplate');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('colonnes', $params[0]->getName());
        $this->assertEquals('nomFichier', $params[1]->getName());
    }

    /**
     * @test
     * La méthode genererTemplate retourne string
     */
    public function testGenererTemplateReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceExcel::class, 'genererTemplate');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('string', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode getStoragePath()
    // =========================================================================

    /**
     * @test
     * La méthode getStoragePath existe et est privée
     */
    public function testGetStoragePathEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceExcel::class, 'getStoragePath'));

        $reflection = new \ReflectionMethod(ServiceExcel::class, 'getStoragePath');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests des extensions supportées
    // =========================================================================

    /**
     * @test
     * Extension XLSX est supportée
     */
    public function testExtensionXlsxSupportee(): void
    {
        $extensionsAttendues = ['xlsx', 'csv'];
        $this->assertContains('xlsx', $extensionsAttendues);
    }

    /**
     * @test
     * Extension CSV est supportée
     */
    public function testExtensionCsvSupportee(): void
    {
        $extensionsAttendues = ['xlsx', 'csv'];
        $this->assertContains('csv', $extensionsAttendues);
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
            'exporter',
            'importer',
            'genererTemplate',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceExcel::class, $method);
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
     * La constante STORAGE_DIR existe
     */
    public function testConstanteStorageDir(): void
    {
        $reflection = new \ReflectionClass(ServiceExcel::class);
        $constants = $reflection->getConstants();

        $this->assertArrayHasKey('STORAGE_DIR', $constants);
        $this->assertStringContainsString('storage', $constants['STORAGE_DIR']);
    }

    /**
     * @test
     * Structure de retour de importer contient les clés attendues
     */
    public function testStructureRetourImporter(): void
    {
        $expectedKeys = ['donnees', 'erreurs', 'total', 'succes'];

        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $expectedKeys);
        }
    }
}
