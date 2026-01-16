<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use App\Controllers\Admin\PermissionsController;

class PermissionsControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(PermissionsController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(PermissionsController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(PermissionsController::class, 'list'));
    }

    public function testMethodRessourcesExiste(): void
    {
        $this->assertTrue(method_exists(PermissionsController::class, 'ressources'));
    }

    public function testMethodUpdateExiste(): void
    {
        $this->assertTrue(method_exists(PermissionsController::class, 'update'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(PermissionsController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
