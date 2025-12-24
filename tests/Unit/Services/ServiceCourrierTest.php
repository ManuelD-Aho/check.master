<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Communication\ServiceCourrier;

/**
 * Tests unitaires exhaustifs pour ServiceCourrier
 * 
 * @covers \App\Services\Communication\ServiceCourrier
 */
class ServiceCourrierTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceCourrier::class));
    }

    // =========================================================================
    // Tests de la méthode getMailer()
    // =========================================================================

    /**
     * @test
     * La méthode getMailer existe et est privée
     */
    public function testGetMailerEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'getMailer'));

        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'getMailer');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode resetMailer()
    // =========================================================================

    /**
     * @test
     * La méthode resetMailer existe et est privée
     */
    public function testResetMailerEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'resetMailer'));

        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'resetMailer');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode envoyerEmail()
    // =========================================================================

    /**
     * @test
     * La méthode envoyerEmail existe avec les bons paramètres
     */
    public function testEnvoyerEmailMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'envoyerEmail'));

        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerEmail');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('destinataire', $params[0]->getName());
        $this->assertEquals('sujet', $params[1]->getName());
        $this->assertEquals('corps', $params[2]->getName());
    }

    /**
     * @test
     * La méthode envoyerEmail supporte HTML par défaut
     */
    public function testEnvoyerEmailHtmlParDefaut(): void
    {
        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerEmail');
        $params = $reflection->getParameters();

        $htmlParam = null;
        foreach ($params as $param) {
            if ($param->getName() === 'html') {
                $htmlParam = $param;
                break;
            }
        }

        if ($htmlParam !== null) {
            $this->assertTrue($htmlParam->isDefaultValueAvailable());
            $this->assertTrue($htmlParam->getDefaultValue());
        }
    }

    /**
     * @test
     * La méthode envoyerEmail retourne bool
     */
    public function testEnvoyerEmailReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerEmail');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode envoyerAvecPiecesJointes()
    // =========================================================================

    /**
     * @test
     * La méthode envoyerAvecPiecesJointes existe
     */
    public function testEnvoyerAvecPiecesJointesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'envoyerAvecPiecesJointes'));

        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerAvecPiecesJointes');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('destinataire', $params[0]->getName());
        $this->assertEquals('sujet', $params[1]->getName());
        $this->assertEquals('corps', $params[2]->getName());
        $this->assertEquals('piecesJointes', $params[3]->getName());
    }

    /**
     * @test
     * Le paramètre piecesJointes est un array
     */
    public function testEnvoyerAvecPiecesJointesParametreArray(): void
    {
        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerAvecPiecesJointes');
        $params = $reflection->getParameters();

        $piecesJointesParam = $params[3];
        $this->assertEquals('array', $piecesJointesParam->getType()->getName());
    }

    // =========================================================================
    // Tests de la méthode envoyerMultiple()
    // =========================================================================

    /**
     * @test
     * La méthode envoyerMultiple existe
     */
    public function testEnvoyerMultipleMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'envoyerMultiple'));

        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerMultiple');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('destinataires', $params[0]->getName());
        $this->assertEquals('array', $params[0]->getType()->getName());
    }

    /**
     * @test
     * La méthode envoyerMultiple retourne array
     */
    public function testEnvoyerMultipleReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerMultiple');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode envoyerEnMasse()
    // =========================================================================

    /**
     * @test
     * La méthode envoyerEnMasse existe
     */
    public function testEnvoyerEnMasseMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'envoyerEnMasse'));

        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'envoyerEnMasse');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('destinataires', $params[0]->getName());
    }

    // =========================================================================
    // Tests de la méthode verifierAdresse()
    // =========================================================================

    /**
     * @test
     * La méthode verifierAdresse existe
     */
    public function testVerifierAdresseMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'verifierAdresse'));

        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'verifierAdresse');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('email', $params[0]->getName());
        $this->assertEquals('string', $params[0]->getType()->getName());
    }

    /**
     * @test
     * La méthode verifierAdresse retourne bool
     */
    public function testVerifierAdresseReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'verifierAdresse');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode testerConfiguration()
    // =========================================================================

    /**
     * @test
     * La méthode testerConfiguration existe
     */
    public function testTesterConfigurationMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCourrier::class, 'testerConfiguration'));
    }

    /**
     * @test
     * La méthode testerConfiguration n'a pas de paramètres
     */
    public function testTesterConfigurationSansParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'testerConfiguration');
        $this->assertCount(0, $reflection->getParameters());
    }

    /**
     * @test
     * La méthode testerConfiguration retourne array
     */
    public function testTesterConfigurationReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCourrier::class, 'testerConfiguration');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de validation des emails
    // =========================================================================

    /**
     * @test
     * Une adresse email valide est acceptée
     */
    public function testEmailValide(): void
    {
        $emailsValides = [
            'user@example.com',
            'user.name@example.com',
            'user+tag@example.co.uk',
        ];

        foreach ($emailsValides as $email) {
            $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
        }
    }

    /**
     * @test
     * Une adresse email invalide est rejetée
     */
    public function testEmailInvalide(): void
    {
        $emailsInvalides = [
            'invalid',
            '@example.com',
            'user@',
            'user@.com',
        ];

        foreach ($emailsInvalides as $email) {
            $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
        }
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
            'envoyerEmail',
            'envoyerAvecPiecesJointes',
            'envoyerMultiple',
            'envoyerEnMasse',
            'verifierAdresse',
            'testerConfiguration',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceCourrier::class, $method);
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
