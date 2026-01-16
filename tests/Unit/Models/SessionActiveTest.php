<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\SessionActive;

class SessionActiveTest extends TestCase
{
    public function testConstanteDureeVieDefinies(): void
    {
        $this->assertEquals(28800, SessionActive::DUREE_VIE_SESSION);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(SessionActive::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new SessionActive([]);
        $this->assertEquals('sessions_actives', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(SessionActive::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new SessionActive([]);
        $this->assertEquals('id_session', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(SessionActive::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new SessionActive([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('token_session', $fillable);
        $this->assertContains('ip_adresse', $fillable);
        $this->assertContains('derniere_activite', $fillable);
        $this->assertContains('expire_a', $fillable);
    }
}
