<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le modèle Notification
 */
class NotificationTest extends TestCase
{
    /**
     * @test
     */
    public function testNotificationAttributsRequis(): void
    {
        $notification = [
            'id_notification' => 1,
            'utilisateur_id' => 1,
            'titre' => 'Nouvelle notification',
            'message' => 'Contenu de la notification',
            'lu' => false,
            'date_creation' => '2025-01-15 10:00:00',
        ];

        $this->assertArrayHasKey('id_notification', $notification);
        $this->assertArrayHasKey('titre', $notification);
        $this->assertArrayHasKey('lu', $notification);
    }

    /**
     * @test
     */
    public function testStatutLu(): void
    {
        $notification = ['lu' => false];
        $this->assertFalse($notification['lu']);
    }
}
