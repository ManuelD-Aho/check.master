<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\JuryMembre;

class JuryMembreTest extends TestCase
{
    public function testConstantesRolesDefinies(): void
    {
        $this->assertEquals('President', JuryMembre::ROLE_PRESIDENT);
        $this->assertEquals('Rapporteur', JuryMembre::ROLE_RAPPORTEUR);
        $this->assertEquals('Examinateur', JuryMembre::ROLE_EXAMINATEUR);
        $this->assertEquals('Encadreur', JuryMembre::ROLE_ENCADREUR);
    }

    public function testConstantesStatutsDefinies(): void
    {
        $this->assertEquals('Invite', JuryMembre::STATUT_INVITE);
        $this->assertEquals('Accepte', JuryMembre::STATUT_ACCEPTE);
        $this->assertEquals('Refuse', JuryMembre::STATUT_REFUSE);
    }

    public function testMethodeGetDossierExiste(): void
    {
        $this->assertTrue(method_exists(JuryMembre::class, 'getDossier'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(JuryMembre::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new JuryMembre([]);
        $this->assertEquals('jury_membres', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(JuryMembre::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new JuryMembre([]);
        $this->assertEquals('id_membre_jury', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(JuryMembre::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new JuryMembre([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('dossier_id', $fillable);
        $this->assertContains('enseignant_id', $fillable);
        $this->assertContains('role_jury', $fillable);
        $this->assertContains('statut_acceptation', $fillable);
    }
}
