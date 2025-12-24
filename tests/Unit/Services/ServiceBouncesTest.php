<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Communication\ServiceBounces;

/**
 * Tests unitaires pour ServiceBounces
 * 
 * @covers \App\Services\Communication\ServiceBounces
 */
class ServiceBouncesTest extends TestCase
{
    /**
     * @test
     * Test de la structure de la classe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceBounces::class));
    }

    /**
     * @test
     * Test de la méthode enregistrerEchec
     */
    public function testEnregistrerEchecMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'enregistrerEchec'));

        $reflection = new \ReflectionMethod(ServiceBounces::class, 'enregistrerEchec');
        $params = $reflection->getParameters();

        $this->assertEquals('email', $params[0]->getName());
        $this->assertEquals('messageErreur', $params[1]->getName());
        $this->assertEquals('hardBounce', $params[2]->getName());
    }

    /**
     * @test
     * Test de la méthode enregistrerHardBounce
     */
    public function testEnregistrerHardBounceMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'enregistrerHardBounce'));
    }

    /**
     * @test
     * Test de la méthode enregistrerSoftBounce
     */
    public function testEnregistrerSoftBounceMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'enregistrerSoftBounce'));
    }

    /**
     * @test
     * Test de la méthode estBloque
     */
    public function testEstBloqueMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'estBloque'));

        $reflection = new \ReflectionMethod(ServiceBounces::class, 'estBloque');
        $returnType = $reflection->getReturnType();

        $this->assertEquals('bool', $returnType->getName());
    }

    /**
     * @test
     * Test de la méthode debloquer
     */
    public function testDebloquerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'debloquer'));

        $reflection = new \ReflectionMethod(ServiceBounces::class, 'debloquer');
        $returnType = $reflection->getReturnType();

        $this->assertEquals('bool', $returnType->getName());
    }

    /**
     * @test
     * Test de la méthode getInfos
     */
    public function testGetInfosMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'getInfos'));
    }

    /**
     * @test
     * Test de la méthode getAdressesBloquees
     */
    public function testGetAdressesBloqueesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'getAdressesBloquees'));

        $reflection = new \ReflectionMethod(ServiceBounces::class, 'getAdressesBloquees');
        $returnType = $reflection->getReturnType();

        $this->assertEquals('array', $returnType->getName());
    }

    /**
     * @test
     * Test de la méthode getStatistiques
     */
    public function testGetStatistiquesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'getStatistiques'));

        $reflection = new \ReflectionMethod(ServiceBounces::class, 'getStatistiques');
        $returnType = $reflection->getReturnType();

        $this->assertEquals('array', $returnType->getName());
    }

    /**
     * @test
     * Test de la méthode nettoyer
     */
    public function testNettoyerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'nettoyer'));

        $reflection = new \ReflectionMethod(ServiceBounces::class, 'nettoyer');
        $params = $reflection->getParameters();

        $this->assertEquals('joursRetention', $params[0]->getName());
        $this->assertEquals(90, $params[0]->getDefaultValue());
    }

    /**
     * @test
     * Test de la méthode reinitialiserCompteur
     */
    public function testReinitialiserCompteurMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceBounces::class, 'reinitialiserCompteur'));
    }

    /**
     * @test
     * Test du seuil de soft bounces
     */
    public function testSeuilSoftBounceDefini(): void
    {
        $reflection = new \ReflectionClass(ServiceBounces::class);
        $this->assertTrue($reflection->hasConstant('SEUIL_SOFT_BOUNCE'));
        $this->assertEquals(5, $reflection->getConstant('SEUIL_SOFT_BOUNCE'));
    }

    /**
     * @test
     * Test que toutes les méthodes sont statiques
     */
    public function testMethodesStatiques(): void
    {
        $methods = [
            'enregistrerEchec',
            'enregistrerHardBounce',
            'enregistrerSoftBounce',
            'estBloque',
            'debloquer',
            'getInfos',
            'getAdressesBloquees',
            'getStatistiques',
            'nettoyer',
            'reinitialiserCompteur',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceBounces::class, $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode {$method} devrait être statique"
            );
        }
    }
}
