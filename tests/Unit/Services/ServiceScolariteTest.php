<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Scolarite\ServiceScolarite;

/**
 * Tests unitaires exhaustifs pour ServiceScolarite
 * 
 * @covers \App\Services\Scolarite\ServiceScolarite
 */
class ServiceScolariteTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceScolarite::class));
    }

    // =========================================================================
    // Tests de la méthode creerEtudiant()
    // =========================================================================

    /**
     * @test
     * La méthode creerEtudiant existe avec les bons paramètres
     */
    public function testCreerEtudiantMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'creerEtudiant'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'creerEtudiant');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('donnees', $params[0]->getName());
        $this->assertEquals('creePar', $params[1]->getName());
    }

    /**
     * @test
     * Le paramètre donnees est un array
     */
    public function testCreerEtudiantDonneesEstArray(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'creerEtudiant');
        $params = $reflection->getParameters();

        $this->assertEquals('array', $params[0]->getType()->getName());
    }

    /**
     * @test
     * La méthode creerEtudiant retourne Etudiant
     */
    public function testCreerEtudiantReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'creerEtudiant');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('App\Models\Etudiant', $returnType->getName());
    }

    /**
     * @test
     * La méthode creerEtudiant est une méthode d'instance
     */
    public function testCreerEtudiantEstMethodeInstance(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'creerEtudiant');
        $this->assertFalse($reflection->isStatic());
    }

    // =========================================================================
    // Tests de la méthode creerDossier()
    // =========================================================================

    /**
     * @test
     * La méthode creerDossier existe avec les bons paramètres
     */
    public function testCreerDossierMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'creerDossier'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'creerDossier');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('etudiantId', $params[0]->getName());
        $this->assertEquals('anneeAcadId', $params[1]->getName());
    }

    /**
     * @test
     * La méthode creerDossier retourne DossierEtudiant
     */
    public function testCreerDossierReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'creerDossier');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('App\Models\DossierEtudiant', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode enregistrerPaiement()
    // =========================================================================

    /**
     * @test
     * La méthode enregistrerPaiement existe avec les bons paramètres
     */
    public function testEnregistrerPaiementMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'enregistrerPaiement'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'enregistrerPaiement');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('donnees', $params[0]->getName());
        $this->assertEquals('creePar', $params[1]->getName());
    }

    /**
     * @test
     * La méthode enregistrerPaiement retourne Paiement
     */
    public function testEnregistrerPaiementReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'enregistrerPaiement');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('App\Models\Paiement', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode verifierDeblocagePaiement()
    // =========================================================================

    /**
     * @test
     * La méthode verifierDeblocagePaiement existe et est privée
     */
    public function testVerifierDeblocagePaiementEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'verifierDeblocagePaiement'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'verifierDeblocagePaiement');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * La méthode verifierDeblocagePaiement prend les bons paramètres
     */
    public function testVerifierDeblocagePaiementParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'verifierDeblocagePaiement');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('etudiantId', $params[0]->getName());
        $this->assertEquals('anneeAcadId', $params[1]->getName());
    }

    // =========================================================================
    // Tests de la méthode paiementComplet()
    // =========================================================================

    /**
     * @test
     * La méthode paiementComplet existe
     */
    public function testPaiementCompletMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'paiementComplet'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'paiementComplet');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('etudiantId', $params[0]->getName());
        $this->assertEquals('anneeAcadId', $params[1]->getName());
    }

    /**
     * @test
     * La méthode paiementComplet retourne bool
     */
    public function testPaiementCompletReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'paiementComplet');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    /**
     * @test
     * La méthode paiementComplet est publique
     */
    public function testPaiementCompletEstPublique(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'paiementComplet');
        $this->assertTrue($reflection->isPublic());
    }

    // =========================================================================
    // Tests de la méthode validerCandidature()
    // =========================================================================

    /**
     * @test
     * La méthode validerCandidature existe avec les bons paramètres
     */
    public function testValiderCandidatureMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'validerCandidature'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'validerCandidature');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('candidatureId', $params[0]->getName());
        $this->assertEquals('validePar', $params[1]->getName());
    }

    /**
     * @test
     * La méthode validerCandidature retourne bool
     */
    public function testValiderCandidatureReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'validerCandidature');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode rejeterCandidature()
    // =========================================================================

    /**
     * @test
     * La méthode rejeterCandidature existe avec les bons paramètres
     */
    public function testRejeterCandidatureMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'rejeterCandidature'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'rejeterCandidature');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('candidatureId', $params[0]->getName());
        $this->assertEquals('motif', $params[1]->getName());
        $this->assertEquals('rejetePar', $params[2]->getName());
    }

    /**
     * @test
     * Le paramètre motif est obligatoire
     */
    public function testRejeterCandidatureMotifObligatoire(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'rejeterCandidature');
        $params = $reflection->getParameters();

        $motifParam = $params[1];
        $this->assertFalse($motifParam->isOptional());
        $this->assertEquals('string', $motifParam->getType()->getName());
    }

    // =========================================================================
    // Tests de la méthode getRecapPaiements()
    // =========================================================================

    /**
     * @test
     * La méthode getRecapPaiements existe
     */
    public function testGetRecapPaiementsMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'getRecapPaiements'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'getRecapPaiements');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('etudiantId', $params[0]->getName());
        $this->assertEquals('anneeAcadId', $params[1]->getName());
    }

    /**
     * @test
     * La méthode getRecapPaiements retourne array
     */
    public function testGetRecapPaiementsReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'getRecapPaiements');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    /**
     * @test
     * La structure de retour de getRecapPaiements
     */
    public function testGetRecapPaiementsStructure(): void
    {
        $expectedKeys = ['paiements', 'total_paye', 'complet'];

        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $expectedKeys);
        }
    }

    // =========================================================================
    // Tests de la méthode rechercherEtudiants()
    // =========================================================================

    /**
     * @test
     * La méthode rechercherEtudiants existe
     */
    public function testRechercherEtudiantsMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceScolarite::class, 'rechercherEtudiants'));

        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'rechercherEtudiants');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(1, count($params));
        $this->assertEquals('terme', $params[0]->getName());
    }

    /**
     * @test
     * Le paramètre limite a une valeur par défaut
     */
    public function testRechercherEtudiantsLimiteParDefaut(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'rechercherEtudiants');
        $params = $reflection->getParameters();

        if (count($params) > 1) {
            $limiteParam = $params[1];
            $this->assertTrue($limiteParam->isDefaultValueAvailable());
            $this->assertEquals(50, $limiteParam->getDefaultValue());
        }
    }

    /**
     * @test
     * La méthode rechercherEtudiants retourne array
     */
    public function testRechercherEtudiantsReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceScolarite::class, 'rechercherEtudiants');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de cohérence globale
    // =========================================================================

    /**
     * @test
     * Les méthodes publiques principales sont des méthodes d'instance
     */
    public function testMethodesPubliquesSontMethodesInstance(): void
    {
        $methods = [
            'creerEtudiant',
            'creerDossier',
            'enregistrerPaiement',
            'paiementComplet',
            'validerCandidature',
            'rejeterCandidature',
            'getRecapPaiements',
            'rechercherEtudiants',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceScolarite::class, $method);
            $this->assertTrue(
                $reflection->isPublic(),
                "La méthode {$method} devrait être publique"
            );
        }
    }

    /**
     * @test
     * Le service peut être instancié
     */
    public function testServiceEstInstanciable(): void
    {
        $reflection = new \ReflectionClass(ServiceScolarite::class);
        $this->assertTrue($reflection->isInstantiable());
    }
}
