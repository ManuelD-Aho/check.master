<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\NotificationTemplate;

class NotificationTemplateTest extends TestCase
{
    public function testConstantesCanauxDefinies(): void
    {
        $this->assertEquals('Email', NotificationTemplate::CANAL_EMAIL);
        $this->assertEquals('SMS', NotificationTemplate::CANAL_SMS);
        $this->assertEquals('Messagerie', NotificationTemplate::CANAL_MESSAGERIE);
    }

    public function testMethodeFindByCodeExiste(): void
    {
        $this->assertTrue(method_exists(NotificationTemplate::class, 'findByCode'));
        
        $reflection = new \ReflectionMethod(NotificationTemplate::class, 'findByCode');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(NotificationTemplate::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new NotificationTemplate([]);
        $this->assertEquals('notification_templates', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(NotificationTemplate::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new NotificationTemplate([]);
        $this->assertEquals('id_template', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(NotificationTemplate::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new NotificationTemplate([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('code_template', $fillable);
        $this->assertContains('canal', $fillable);
        $this->assertContains('sujet', $fillable);
        $this->assertContains('corps', $fillable);
        $this->assertContains('actif', $fillable);
    }
}
