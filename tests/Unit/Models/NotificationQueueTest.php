<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\NotificationQueue;

class NotificationQueueTest extends TestCase
{
    public function testConstantesStatutsDefinies(): void
    {
        $this->assertEquals('En_attente', NotificationQueue::STATUT_EN_ATTENTE);
        $this->assertEquals('En_cours', NotificationQueue::STATUT_EN_COURS);
        $this->assertEquals('Envoye', NotificationQueue::STATUT_ENVOYE);
        $this->assertEquals('Echec', NotificationQueue::STATUT_ECHEC);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(NotificationQueue::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new NotificationQueue([]);
        $this->assertEquals('notifications_queue', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(NotificationQueue::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new NotificationQueue([]);
        $this->assertEquals('id_queue', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(NotificationQueue::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new NotificationQueue([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('template_id', $fillable);
        $this->assertContains('destinataire_id', $fillable);
        $this->assertContains('canal', $fillable);
        $this->assertContains('priorite', $fillable);
        $this->assertContains('statut', $fillable);
    }
}
