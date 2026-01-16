<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\TypeUtilisateur;

class TypeUtilisateurTest extends TestCase
{
    public function testConstantesTypesDefinies(): void
    {
        $this->assertEquals('Etudiant', TypeUtilisateur::TYPE_ETUDIANT);
        $this->assertEquals('Enseignant', TypeUtilisateur::TYPE_ENSEIGNANT);
        $this->assertEquals('Personnel', TypeUtilisateur::TYPE_PERSONNEL);
        $this->assertEquals('Administrateur', TypeUtilisateur::TYPE_ADMINISTRATEUR);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(TypeUtilisateur::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new TypeUtilisateur([]);
        $this->assertEquals('type_utilisateur', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(TypeUtilisateur::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new TypeUtilisateur([]);
        $this->assertEquals('id_type_utilisateur', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(TypeUtilisateur::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new TypeUtilisateur([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_type_utilisateur', $fillable);
        $this->assertContains('description', $fillable);
    }
}
