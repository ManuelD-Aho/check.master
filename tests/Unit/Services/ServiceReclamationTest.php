<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Incidents\ServiceReclamation;

/**
 * Tests unitaires exhaustifs pour ServiceReclamation
 * 
 * @covers \App\Services\Incidents\ServiceReclamation
 */
class ServiceReclamationTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceReclamation::class));
    }

    // =========================================================================
    // Tests des constantes de statut
    // =========================================================================

    /**
     * @test
     * La constante STATUT_SOUMISE existe
     */
    public function testConstanteStatutSoumise(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::STATUT_SOUMISE'));
        $this->assertEquals('Soumise', ServiceReclamation::STATUT_SOUMISE);
    }

    /**
     * @test
     * La constante STATUT_EN_COURS existe
     */
    public function testConstanteStatutEnCours(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::STATUT_EN_COURS'));
        $this->assertEquals('En_cours', ServiceReclamation::STATUT_EN_COURS);
    }

    /**
     * @test
     * La constante STATUT_RESOLUE existe
     */
    public function testConstanteStatutResolue(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::STATUT_RESOLUE'));
        $this->assertEquals('Resolue', ServiceReclamation::STATUT_RESOLUE);
    }

    /**
     * @test
     * La constante STATUT_REJETEE existe
     */
    public function testConstanteStatutRejetee(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::STATUT_REJETEE'));
        $this->assertEquals('Rejetee', ServiceReclamation::STATUT_REJETEE);
    }

    // =========================================================================
    // Tests des constantes de type
    // =========================================================================

    /**
     * @test
     * La constante TYPE_NOTE existe
     */
    public function testConstanteTypeNote(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::TYPE_NOTE'));
        $this->assertEquals('note', ServiceReclamation::TYPE_NOTE);
    }

    /**
     * @test
     * La constante TYPE_PAIEMENT existe
     */
    public function testConstanteTypePaiement(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::TYPE_PAIEMENT'));
        $this->assertEquals('paiement', ServiceReclamation::TYPE_PAIEMENT);
    }

    /**
     * @test
     * La constante TYPE_ADMINISTRATIF existe
     */
    public function testConstanteTypeAdministratif(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::TYPE_ADMINISTRATIF'));
        $this->assertEquals('administratif', ServiceReclamation::TYPE_ADMINISTRATIF);
    }

    /**
     * @test
     * La constante TYPE_TECHNIQUE existe
     */
    public function testConstanteTypeTechnique(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::TYPE_TECHNIQUE'));
        $this->assertEquals('technique', ServiceReclamation::TYPE_TECHNIQUE);
    }

    /**
     * @test
     * La constante TYPE_AUTRE existe
     */
    public function testConstanteTypeAutre(): void
    {
        $this->assertTrue(defined(ServiceReclamation::class . '::TYPE_AUTRE'));
        $this->assertEquals('autre', ServiceReclamation::TYPE_AUTRE);
    }

    // =========================================================================
    // Tests de la méthode soumettre()
    // =========================================================================

    /**
     * @test
     * La méthode soumettre existe avec les bons paramètres
     */
    public function testSoumettreMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'soumettre'));

        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'soumettre');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('etudiantId', $params[0]->getName());
        $this->assertEquals('type', $params[1]->getName());
        $this->assertEquals('sujet', $params[2]->getName());
        $this->assertEquals('description', $params[3]->getName());
    }

    /**
     * @test
     * La méthode soumettre retourne Reclamation
     */
    public function testSoumettreReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'soumettre');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('App\Models\Reclamation', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode prendreEnCharge()
    // =========================================================================

    /**
     * @test
     * La méthode prendreEnCharge existe
     */
    public function testPrendreEnChargeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'prendreEnCharge'));

        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'prendreEnCharge');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('reclamationId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
    }

    /**
     * @test
     * La méthode prendreEnCharge retourne bool
     */
    public function testPrendreEnChargeReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'prendreEnCharge');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode resoudre()
    // =========================================================================

    /**
     * @test
     * La méthode resoudre existe
     */
    public function testResoudreMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'resoudre'));

        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'resoudre');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('reclamationId', $params[0]->getName());
        $this->assertEquals('resolution', $params[1]->getName());
        $this->assertEquals('resoluepar', $params[2]->getName());
    }

    /**
     * @test
     * La méthode resoudre retourne bool
     */
    public function testResoudreReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'resoudre');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode rejeter()
    // =========================================================================

    /**
     * @test
     * La méthode rejeter existe
     */
    public function testRejeterMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'rejeter'));

        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'rejeter');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('reclamationId', $params[0]->getName());
        $this->assertEquals('motif', $params[1]->getName());
        $this->assertEquals('rejetePar', $params[2]->getName());
    }

    /**
     * @test
     * La méthode rejeter retourne bool
     */
    public function testRejeterReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'rejeter');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode notifierEtudiant()
    // =========================================================================

    /**
     * @test
     * La méthode notifierEtudiant existe et est privée
     */
    public function testNotifierEtudiantEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'notifierEtudiant'));

        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'notifierEtudiant');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode getReclamationsEtudiant()
    // =========================================================================

    /**
     * @test
     * La méthode getReclamationsEtudiant existe
     */
    public function testGetReclamationsEtudiantMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'getReclamationsEtudiant'));

        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'getReclamationsEtudiant');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('etudiantId', $params[0]->getName());
    }

    /**
     * @test
     * La méthode getReclamationsEtudiant retourne array
     */
    public function testGetReclamationsEtudiantReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'getReclamationsEtudiant');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode getReclamationsEnAttente()
    // =========================================================================

    /**
     * @test
     * La méthode getReclamationsEnAttente existe
     */
    public function testGetReclamationsEnAttenteMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'getReclamationsEnAttente'));
    }

    /**
     * @test
     * La méthode getReclamationsEnAttente n'a pas de paramètres
     */
    public function testGetReclamationsEnAttenteSansParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'getReclamationsEnAttente');
        $this->assertCount(0, $reflection->getParameters());
    }

    // =========================================================================
    // Tests de la méthode getReclamationsEnCours()
    // =========================================================================

    /**
     * @test
     * La méthode getReclamationsEnCours existe
     */
    public function testGetReclamationsEnCoursMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceReclamation::class, 'getReclamationsEnCours'));
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
        $this->assertTrue(method_exists(ServiceReclamation::class, 'getStatistiques'));
    }

    /**
     * @test
     * La méthode getStatistiques retourne array
     */
    public function testGetStatistiquesReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceReclamation::class, 'getStatistiques');
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
            'soumettre',
            'prendreEnCharge',
            'resoudre',
            'rejeter',
            'getReclamationsEtudiant',
            'getReclamationsEnAttente',
            'getReclamationsEnCours',
            'getStatistiques',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceReclamation::class, $method);
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
}
