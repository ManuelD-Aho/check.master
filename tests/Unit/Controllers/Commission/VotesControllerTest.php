<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Commission;

use PHPUnit\Framework\TestCase;
use App\Controllers\Commission\VotesController;

class VotesControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(VotesController::class));
    }

    public function testMethodIndexExiste(): void
    {
        $this->assertTrue(method_exists(VotesController::class, 'index'));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(VotesController::class, 'list'));
    }

    public function testMethodVoterExiste(): void
    {
        $this->assertTrue(method_exists(VotesController::class, 'voter'));
    }

    public function testIndexRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(VotesController::class, 'index');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
