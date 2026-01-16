<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers\Communication;

use PHPUnit\Framework\TestCase;
use App\Controllers\Communication\NotificationsController;

class NotificationsControllerTest extends TestCase
{
    public function testControllerClassExists(): void
    {
        $this->assertTrue(class_exists(NotificationsController::class));
    }

    public function testMethodListExiste(): void
    {
        $this->assertTrue(method_exists(NotificationsController::class, 'list'));
    }

    public function testMethodHistoriqueExiste(): void
    {
        $this->assertTrue(method_exists(NotificationsController::class, 'historique'));
    }

    public function testListRetourneResponse(): void
    {
        $reflection = new \ReflectionMethod(NotificationsController::class, 'list');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
    }
}
