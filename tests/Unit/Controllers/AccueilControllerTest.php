<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AccueilController;

class AccueilControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(AccueilController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(AccueilController::class, 'index'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(AccueilController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('Src\\Http\\Response', $returnType->getName());
    }

    public function testIndexEstPublique(): void
    {
        $reflection = new \ReflectionMethod(AccueilController::class, 'index');
        $this->assertTrue($reflection->isPublic());
    }
}
