<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Soutenance;

use PHPUnit\Framework\TestCase;
use App\Controllers\Soutenance\JuryController;

class JuryControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(JuryController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(JuryController::class, 'index'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(JuryController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }

    public function testConstructeurExiste(): void
    {
        $reflection = new \ReflectionClass(JuryController::class);
        $this->assertTrue($reflection->hasMethod('__construct'));
    }
}
