<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\MessageInterne;

class MessageInterneTest extends TestCase
{
    public function testMethodeExpediteurExiste(): void
    {
        $this->assertTrue(method_exists(MessageInterne::class, 'expediteur'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(MessageInterne::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new MessageInterne([]);
        $this->assertEquals('messages_internes', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(MessageInterne::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new MessageInterne([]);
        $this->assertEquals('id_message', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(MessageInterne::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new MessageInterne([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('expediteur_id', $fillable);
        $this->assertContains('destinataire_id', $fillable);
        $this->assertContains('sujet', $fillable);
        $this->assertContains('contenu', $fillable);
        $this->assertContains('lu', $fillable);
    }
}
