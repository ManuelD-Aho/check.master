<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Pister;

class PisterTest extends TestCase
{
    public function testConstantesActionsDefinies(): void
    {
        $this->assertEquals('connexion', Pister::ACTION_CONNEXION);
        $this->assertEquals('deconnexion', Pister::ACTION_DECONNEXION);
        $this->assertEquals('echec_connexion', Pister::ACTION_ECHEC_CONNEXION);
    }

    public function testConstantesActionsCRUDDefinies(): void
    {
        $this->assertTrue(defined(Pister::class . '::ACTION_LOGIN'));
        $this->assertTrue(defined(Pister::class . '::ACTION_LOGOUT'));
        $this->assertTrue(defined(Pister::class . '::ACTION_LOGIN_ECHEC'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Pister::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Pister([]);
        $this->assertEquals('pister', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Pister::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Pister([]);
        $this->assertEquals('id_pister', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Pister::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Pister([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('action', $fillable);
        $this->assertContains('entite_type', $fillable);
        $this->assertContains('donnees_snapshot', $fillable);
        $this->assertContains('ip_adresse', $fillable);
    }
}
