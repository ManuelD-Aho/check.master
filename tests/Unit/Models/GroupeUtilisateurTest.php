<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\GroupeUtilisateur;

class GroupeUtilisateurTest extends TestCase
{
    public function testConstantesGroupesDefinies(): void
    {
        $this->assertEquals(5, GroupeUtilisateur::GROUPE_ADMIN);
        $this->assertEquals(6, GroupeUtilisateur::GROUPE_SECRETAIRE);
        $this->assertEquals(7, GroupeUtilisateur::GROUPE_COMMUNICATION);
        $this->assertEquals(8, GroupeUtilisateur::GROUPE_SCOLARITE);
        $this->assertEquals(9, GroupeUtilisateur::GROUPE_RESP_FILIERE);
        $this->assertEquals(10, GroupeUtilisateur::GROUPE_RESP_NIVEAU);
        $this->assertEquals(11, GroupeUtilisateur::GROUPE_COMMISSION);
        $this->assertEquals(12, GroupeUtilisateur::GROUPE_ENSEIGNANT);
        $this->assertEquals(13, GroupeUtilisateur::GROUPE_ETUDIANT);
    }

    public function testMethodeUtilisateursExiste(): void
    {
        $this->assertTrue(method_exists(GroupeUtilisateur::class, 'utilisateurs'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(GroupeUtilisateur::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new GroupeUtilisateur([]);
        $this->assertEquals('groupe_utilisateur', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(GroupeUtilisateur::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new GroupeUtilisateur([]);
        $this->assertEquals('id_GU', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(GroupeUtilisateur::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new GroupeUtilisateur([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('lib_GU', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('niveau_hierarchique', $fillable);
    }
}
