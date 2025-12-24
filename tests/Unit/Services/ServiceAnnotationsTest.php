<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Rapport\ServiceAnnotations;

/**
 * Tests unitaires exhaustifs pour ServiceAnnotations
 * 
 * @covers \App\Services\Rapport\ServiceAnnotations
 */
class ServiceAnnotationsTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceAnnotations::class));
    }

    // =========================================================================
    // Tests des constantes de type
    // =========================================================================

    /**
     * @test
     * La constante TYPE_COMMENTAIRE existe
     */
    public function testConstanteTypeCommentaire(): void
    {
        $this->assertTrue(defined(ServiceAnnotations::class . '::TYPE_COMMENTAIRE'));
        $this->assertEquals('commentaire', ServiceAnnotations::TYPE_COMMENTAIRE);
    }

    /**
     * @test
     * La constante TYPE_CORRECTION existe
     */
    public function testConstanteTypeCorrection(): void
    {
        $this->assertTrue(defined(ServiceAnnotations::class . '::TYPE_CORRECTION'));
        $this->assertEquals('correction', ServiceAnnotations::TYPE_CORRECTION);
    }

    /**
     * @test
     * La constante TYPE_SUGGESTION existe
     */
    public function testConstanteTypeSuggestion(): void
    {
        $this->assertTrue(defined(ServiceAnnotations::class . '::TYPE_SUGGESTION'));
        $this->assertEquals('suggestion', ServiceAnnotations::TYPE_SUGGESTION);
    }

    /**
     * @test
     * La constante TYPE_ERREUR existe
     */
    public function testConstanteTypeErreur(): void
    {
        $this->assertTrue(defined(ServiceAnnotations::class . '::TYPE_ERREUR'));
        $this->assertEquals('erreur', ServiceAnnotations::TYPE_ERREUR);
    }

    // =========================================================================
    // Tests de la méthode ajouter()
    // =========================================================================

    /**
     * @test
     * La méthode ajouter existe avec les bons paramètres
     */
    public function testAjouterMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceAnnotations::class, 'ajouter'));

        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'ajouter');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('rapportId', $params[0]->getName());
        $this->assertEquals('auteurId', $params[1]->getName());
        $this->assertEquals('page', $params[2]->getName());
        $this->assertEquals('contenu', $params[3]->getName());
    }

    /**
     * @test
     * Le paramètre type a une valeur par défaut
     */
    public function testAjouterTypeParDefaut(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'ajouter');
        $params = $reflection->getParameters();

        $typeParam = null;
        foreach ($params as $param) {
            if ($param->getName() === 'type') {
                $typeParam = $param;
                break;
            }
        }

        if ($typeParam !== null) {
            $this->assertTrue($typeParam->isDefaultValueAvailable());
            $this->assertEquals(ServiceAnnotations::TYPE_COMMENTAIRE, $typeParam->getDefaultValue());
        }
    }

    /**
     * @test
     * La méthode ajouter retourne AnnotationRapport
     */
    public function testAjouterReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'ajouter');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('App\Models\AnnotationRapport', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode modifier()
    // =========================================================================

    /**
     * @test
     * La méthode modifier existe
     */
    public function testModifierMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceAnnotations::class, 'modifier'));

        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'modifier');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('annotationId', $params[0]->getName());
        $this->assertEquals('contenu', $params[1]->getName());
        $this->assertEquals('utilisateurId', $params[2]->getName());
    }

    /**
     * @test
     * La méthode modifier retourne bool
     */
    public function testModifierReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'modifier');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode supprimer()
    // =========================================================================

    /**
     * @test
     * La méthode supprimer existe
     */
    public function testSupprimerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceAnnotations::class, 'supprimer'));

        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'supprimer');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('annotationId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
    }

    /**
     * @test
     * La méthode supprimer retourne bool
     */
    public function testSupprimerReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'supprimer');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode getAnnotationsRapport()
    // =========================================================================

    /**
     * @test
     * La méthode getAnnotationsRapport existe
     */
    public function testGetAnnotationsRapportMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceAnnotations::class, 'getAnnotationsRapport'));

        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'getAnnotationsRapport');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('rapportId', $params[0]->getName());
    }

    /**
     * @test
     * La méthode getAnnotationsRapport retourne array
     */
    public function testGetAnnotationsRapportReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'getAnnotationsRapport');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode getAnnotationsPage()
    // =========================================================================

    /**
     * @test
     * La méthode getAnnotationsPage existe
     */
    public function testGetAnnotationsPageMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceAnnotations::class, 'getAnnotationsPage'));

        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'getAnnotationsPage');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('rapportId', $params[0]->getName());
        $this->assertEquals('page', $params[1]->getName());
    }

    // =========================================================================
    // Tests de la méthode compterParType()
    // =========================================================================

    /**
     * @test
     * La méthode compterParType existe
     */
    public function testCompterParTypeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceAnnotations::class, 'compterParType'));

        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'compterParType');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('rapportId', $params[0]->getName());
    }

    /**
     * @test
     * La méthode compterParType retourne array
     */
    public function testCompterParTypeReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'compterParType');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode marquerResolue()
    // =========================================================================

    /**
     * @test
     * La méthode marquerResolue existe
     */
    public function testMarquerResolueMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceAnnotations::class, 'marquerResolue'));

        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'marquerResolue');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('annotationId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
    }

    /**
     * @test
     * La méthode marquerResolue retourne bool
     */
    public function testMarquerResolueReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'marquerResolue');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
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
            'ajouter',
            'modifier',
            'supprimer',
            'getAnnotationsRapport',
            'getAnnotationsPage',
            'compterParType',
            'marquerResolue',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceAnnotations::class, $method);
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
     * La position JSON est optionnelle
     */
    public function testPositionJsonOptionnelle(): void
    {
        $reflection = new \ReflectionMethod(ServiceAnnotations::class, 'ajouter');
        $params = $reflection->getParameters();

        $positionParam = null;
        foreach ($params as $param) {
            if ($param->getName() === 'position') {
                $positionParam = $param;
                break;
            }
        }

        if ($positionParam !== null) {
            $this->assertTrue($positionParam->isOptional());
            $this->assertTrue($positionParam->allowsNull());
        }
    }
}
