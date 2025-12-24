<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Core\ServiceParametres;

/**
 * Tests unitaires pour ServiceParametres
 * 
 * @covers \App\Services\Core\ServiceParametres
 */
class ServiceParametresTest extends TestCase
{
    /**
     * @test
     * Test de la structure de la classe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceParametres::class));
    }

    /**
     * @test
     * Test de la méthode get
     */
    public function testGetMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceParametres::class, 'get'));

        $reflection = new \ReflectionMethod(ServiceParametres::class, 'get');
        $params = $reflection->getParameters();

        $this->assertEquals('key', $params[0]->getName());
        $this->assertEquals('default', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode set
     */
    public function testSetMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceParametres::class, 'set'));

        $reflection = new \ReflectionMethod(ServiceParametres::class, 'set');
        $params = $reflection->getParameters();

        $this->assertEquals('key', $params[0]->getName());
        $this->assertEquals('value', $params[1]->getName());
        $this->assertEquals('type', $params[2]->getName());
        $this->assertEquals('groupe', $params[3]->getName());
    }

    /**
     * @test
     * Test de la méthode all
     */
    public function testAllMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceParametres::class, 'all'));

        $reflection = new \ReflectionMethod(ServiceParametres::class, 'all');
        $returnType = $reflection->getReturnType();

        $this->assertEquals('array', $returnType->getName());
    }

    /**
     * @test
     * Test que toutes les méthodes sont statiques
     */
    public function testMethodesStatiques(): void
    {
        $methods = ['get', 'set', 'all'];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceParametres::class, $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode {$method} devrait être statique"
            );
        }
    }
}
