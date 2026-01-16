<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\EmailBounce;

class EmailBounceTest extends TestCase
{
    public function testMethodeLogExiste(): void
    {
        $this->assertTrue(method_exists(EmailBounce::class, 'log'));
        
        $reflection = new \ReflectionMethod(EmailBounce::class, 'log');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(EmailBounce::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new EmailBounce([]);
        $this->assertEquals('email_bounces', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(EmailBounce::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new EmailBounce([]);
        $this->assertEquals('id_bounce', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(EmailBounce::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new EmailBounce([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('email', $fillable);
        $this->assertContains('type_bounce', $fillable);
        $this->assertContains('raison', $fillable);
        $this->assertContains('compteur', $fillable);
        $this->assertContains('bloque', $fillable);
    }
}
