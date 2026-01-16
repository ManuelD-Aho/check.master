<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\DossierEtudiant;

class DossierEtudiantTest extends TestCase
{
    public function testMethodeGetEtudiantExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getEtudiant'));
    }

    public function testMethodeGetAnneeAcademiqueExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getAnneeAcademique'));
    }

    public function testMethodeGetEtatActuelExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getEtatActuel'));
    }

    public function testMethodeTransitionnerExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'transitionner'));
        
        $reflection = new \ReflectionMethod(DossierEtudiant::class, 'transitionner');
        $params = $reflection->getParameters();
        
        $this->assertGreaterThanOrEqual(1, count($params));
        $this->assertEquals('codeEtatCible', $params[0]->getName());
    }

    public function testMethodeDelaiDepasseExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'delaiDepasse'));
    }

    public function testMethodePourcentageDelaiExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'pourcentageDelai'));
    }

    public function testMethodeGetCandidatureExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getCandidature'));
    }

    public function testMethodeGetRapportExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getRapport'));
    }

    public function testMethodeGetSoutenanceExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getSoutenance'));
    }

    public function testMethodeGetJuryExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getJury'));
    }

    public function testMethodeGetHistoriqueExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'getHistorique'));
    }

    public function testMethodeTrouverExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'trouver'));
        
        $reflection = new \ReflectionMethod(DossierEtudiant::class, 'trouver');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeFindByEtudiantExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'findByEtudiant'));
        
        $reflection = new \ReflectionMethod(DossierEtudiant::class, 'findByEtudiant');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeParEtatExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'parEtat'));
    }

    public function testMethodeDelaisDepassesExiste(): void
    {
        $this->assertTrue(method_exists(DossierEtudiant::class, 'delaisDepasses'));
        
        $reflection = new \ReflectionMethod(DossierEtudiant::class, 'delaisDepasses');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(DossierEtudiant::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new DossierEtudiant([]);
        $this->assertEquals('dossiers_etudiants', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(DossierEtudiant::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new DossierEtudiant([]);
        $this->assertEquals('id_dossier', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(DossierEtudiant::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new DossierEtudiant([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('etudiant_id', $fillable);
        $this->assertContains('annee_acad_id', $fillable);
        $this->assertContains('etat_actuel_id', $fillable);
        $this->assertContains('date_entree_etat', $fillable);
        $this->assertContains('date_limite_etat', $fillable);
    }
}
