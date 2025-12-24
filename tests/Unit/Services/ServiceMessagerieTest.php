<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Communication\ServiceMessagerie;

/**
 * Tests unitaires pour ServiceMessagerie
 * 
 * @covers \App\Services\Communication\ServiceMessagerie
 */
class ServiceMessagerieTest extends TestCase
{
    /**
     * @test
     * Test de la structure de la classe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceMessagerie::class));
    }

    /**
     * @test
     * Test de la méthode envoyer
     */
    public function testEnvoyerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'envoyer'));

        $reflection = new \ReflectionMethod(ServiceMessagerie::class, 'envoyer');
        $params = $reflection->getParameters();

        $this->assertEquals('expediteurId', $params[0]->getName());
        $this->assertEquals('destinataireId', $params[1]->getName());
        $this->assertEquals('sujet', $params[2]->getName());
        $this->assertEquals('contenu', $params[3]->getName());
    }

    /**
     * @test
     * Test de la méthode envoyerSysteme
     */
    public function testEnvoyerSystemeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'envoyerSysteme'));

        $reflection = new \ReflectionMethod(ServiceMessagerie::class, 'envoyerSysteme');
        $params = $reflection->getParameters();

        $this->assertEquals('destinataireId', $params[0]->getName());
        $this->assertEquals('sujet', $params[1]->getName());
        $this->assertEquals('contenu', $params[2]->getName());
    }

    /**
     * @test
     * Test de la méthode marquerLu
     */
    public function testMarquerLuMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'marquerLu'));

        $reflection = new \ReflectionMethod(ServiceMessagerie::class, 'marquerLu');
        $params = $reflection->getParameters();

        $this->assertEquals('messageId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode marquerPlusieursLus
     */
    public function testMarquerPlusieursLusMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'marquerPlusieursLus'));
    }

    /**
     * @test
     * Test de la méthode supprimer
     */
    public function testSupprimerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'supprimer'));
    }

    /**
     * @test
     * Test de la méthode getMessagesRecus
     */
    public function testGetMessagesRecusMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'getMessagesRecus'));
    }

    /**
     * @test
     * Test de la méthode getMessagesEnvoyes
     */
    public function testGetMessagesEnvoyesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'getMessagesEnvoyes'));
    }

    /**
     * @test
     * Test de la méthode compterNonLus
     */
    public function testCompterNonLusMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'compterNonLus'));
    }

    /**
     * @test
     * Test de la méthode getMessage
     */
    public function testGetMessageMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'getMessage'));
    }

    /**
     * @test
     * Test de la méthode repondre
     */
    public function testRepondreMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'repondre'));

        $reflection = new \ReflectionMethod(ServiceMessagerie::class, 'repondre');
        $params = $reflection->getParameters();

        $this->assertEquals('messageOriginalId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $this->assertEquals('contenu', $params[2]->getName());
    }

    /**
     * @test
     * Test de la méthode envoyerMultiple
     */
    public function testEnvoyerMultipleMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'envoyerMultiple'));
    }

    /**
     * @test
     * Test de la méthode rechercher
     */
    public function testRechercherMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceMessagerie::class, 'rechercher'));

        $reflection = new \ReflectionMethod(ServiceMessagerie::class, 'rechercher');
        $params = $reflection->getParameters();

        $this->assertEquals('utilisateurId', $params[0]->getName());
        $this->assertEquals('terme', $params[1]->getName());
    }

    /**
     * @test
     * Test que toutes les méthodes sont statiques
     */
    public function testMethodesStatiques(): void
    {
        $methods = [
            'envoyer',
            'envoyerSysteme',
            'marquerLu',
            'marquerPlusieursLus',
            'supprimer',
            'getMessagesRecus',
            'getMessagesEnvoyes',
            'compterNonLus',
            'getMessage',
            'repondre',
            'envoyerMultiple',
            'rechercher',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceMessagerie::class, $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode {$method} devrait être statique"
            );
        }
    }
}
