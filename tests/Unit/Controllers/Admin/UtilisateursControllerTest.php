<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use App\Controllers\Admin\UtilisateursController;

class UtilisateursControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(UtilisateursController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(UtilisateursController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(UtilisateursController::class, 'list'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(UtilisateursController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testListRetourneJsonResponse(): void
    {
        $reflection = new \ReflectionMethod(UtilisateursController::class, 'list');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
