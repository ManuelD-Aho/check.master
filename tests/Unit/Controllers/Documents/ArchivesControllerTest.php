<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Documents;

use PHPUnit\Framework\TestCase;
use App\Controllers\Documents\ArchivesController;

class ArchivesControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(ArchivesController::class));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(ArchivesController::class, 'list'));
    }

    public function testListRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(ArchivesController::class, 'list');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
