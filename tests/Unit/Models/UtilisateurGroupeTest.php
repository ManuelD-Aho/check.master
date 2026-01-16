<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\UtilisateurGroupe;

class UtilisateurGroupeTest extends TestCase
{
    public function testMethodeUtilisateurExiste(): void
    {
        $this->assertTrue(method_exists(UtilisateurGroupe::class, 'utilisateur'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(UtilisateurGroupe::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new UtilisateurGroupe([]);
        $this->assertEquals('utilisateurs_groupes', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(UtilisateurGroupe::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new UtilisateurGroupe([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('groupe_id', $fillable);
        $this->assertContains('attribue_par', $fillable);
        $this->assertContains('attribue_le', $fillable);
    }
}
