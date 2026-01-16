<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Documents;

use PHPUnit\Framework\TestCase;
use App\Controllers\Documents\DocumentsController;

class DocumentsControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(DocumentsController::class));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(DocumentsController::class, 'list'));
    }

    public function testListRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(DocumentsController::class, 'list');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
