<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\NotificationHistorique;

class NotificationHistoriqueTest extends TestCase
{
    public function testConstantesStatutsDefinies(): void
    {
        $this->assertEquals('Envoye', NotificationHistorique::STATUT_ENVOYE);
        $this->assertEquals('Echec', NotificationHistorique::STATUT_ECHEC);
        $this->assertEquals('Bounce', NotificationHistorique::STATUT_BOUNCE);
    }

    public function testConstantesCanauxDefinies(): void
    {
        $this->assertEquals('Email', NotificationHistorique::CANAL_EMAIL);
        $this->assertEquals('SMS', NotificationHistorique::CANAL_SMS);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(NotificationHistorique::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new NotificationHistorique([]);
        $this->assertEquals('notifications_historique', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(NotificationHistorique::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new NotificationHistorique([]);
        $this->assertEquals('id_historique', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(NotificationHistorique::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new NotificationHistorique([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('template_code', $fillable);
        $this->assertContains('destinataire_id', $fillable);
        $this->assertContains('canal', $fillable);
        $this->assertContains('statut', $fillable);
    }
}
