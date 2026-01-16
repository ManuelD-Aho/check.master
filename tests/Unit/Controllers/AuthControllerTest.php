<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;

class AuthControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(AuthController::class));
    }

    public function testMethodLoginExiste(): void
    {
        $this->assertTrue(method_exists(AuthController::class, 'login'));
    }

    public function testMethodLogoutExiste(): void
    {
        $this->assertTrue(method_exists(AuthController::class, 'logout'));
    }

    public function testMethodForgotPasswordExiste(): void
    {
        $this->assertTrue(method_exists(AuthController::class, 'forgotPassword'));
    }

    public function testMethodChangePasswordExiste(): void
    {
        $this->assertTrue(method_exists(AuthController::class, 'changePassword'));
    }

    public function testLoginRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(AuthController::class, 'login');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('Src\\Http\\Response', $returnType->getName());
    }

    public function testLogoutRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(AuthController::class, 'logout');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('Src\\Http\\Response', $returnType->getName());
    }

    public function testProcessLoginEstPrivee(): void
    {
        $reflection = new \ReflectionMethod(AuthController::class, 'processLogin');
        $this->assertTrue($reflection->isPrivate());
    }

    public function testConstructeurInitialiseService(): void
    {
        $controller = new AuthController();
        
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('authService');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue($controller));
    }
}
