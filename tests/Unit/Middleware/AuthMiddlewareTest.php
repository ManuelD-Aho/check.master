<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\AuthMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour AuthMiddleware
 */
class AuthMiddlewareTest extends TestCase
{
    private AuthMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new AuthMiddleware();
    }

    /**
     * @test
     */
    public function testMiddlewareInstantiation(): void
    {
        $this->assertInstanceOf(AuthMiddleware::class, $this->middleware);
    }

    /**
     * @test
     */
    public function testIsAuthenticatedReturnsFalseWhenNoSession(): void
    {
        // Sans session active, l'authentification devrait échouer
        $this->assertFalse(AuthMiddleware::isAuthenticated());
    }

    /**
     * @test
     */
    public function testGetUserReturnsNullWhenNotAuthenticated(): void
    {
        $this->assertNull(AuthMiddleware::getUser());
    }

    /**
     * @test
     */
    public function testRoutePubliqueSlash(): void
    {
        // Test via réflexion pour tester méthode privée
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('estRoutePublique');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->middleware, '/'));
    }

    /**
     * @test
     */
    public function testRoutePubliqueConnexion(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('estRoutePublique');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->middleware, '/connexion'));
    }

    /**
     * @test
     */
    public function testRoutePubliqueAssets(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('estRoutePublique');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->middleware, '/assets/css/style.css'));
        $this->assertTrue($method->invoke($this->middleware, '/css/main.css'));
        $this->assertTrue($method->invoke($this->middleware, '/js/app.js'));
        $this->assertTrue($method->invoke($this->middleware, '/images/logo.png'));
    }

    /**
     * @test
     */
    public function testRouteProtegee(): void
    {
        $reflection = new \ReflectionClass($this->middleware);
        $method = $reflection->getMethod('estRoutePublique');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($this->middleware, '/dashboard'));
        $this->assertFalse($method->invoke($this->middleware, '/admin/users'));
        $this->assertFalse($method->invoke($this->middleware, '/etudiant/rapport'));
    }
}
