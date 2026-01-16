<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\AnnotationRapport;

class AnnotationRapportTest extends TestCase
{
    public function testConstantesTypesDefinies(): void
    {
        $this->assertEquals('Commentaire', AnnotationRapport::TYPE_COMMENTAIRE);
        $this->assertEquals('Correction', AnnotationRapport::TYPE_CORRECTION);
        $this->assertEquals('Suggestion', AnnotationRapport::TYPE_SUGGESTION);
    }

    public function testTypesValidesContientTousLesTypes(): void
    {
        $typesValides = AnnotationRapport::TYPES_VALIDES;
        
        $this->assertCount(3, $typesValides);
        $this->assertContains(AnnotationRapport::TYPE_COMMENTAIRE, $typesValides);
        $this->assertContains(AnnotationRapport::TYPE_CORRECTION, $typesValides);
        $this->assertContains(AnnotationRapport::TYPE_SUGGESTION, $typesValides);
    }

    public function testMethodeRapportExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'rapport'));
    }

    public function testMethodeAuteurExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'auteur'));
    }

    public function testMethodePourRapportExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'pourRapport'));
        
        $reflection = new \ReflectionMethod(AnnotationRapport::class, 'pourRapport');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeCreerExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'creer'));
        
        $reflection = new \ReflectionMethod(AnnotationRapport::class, 'creer');
        $params = $reflection->getParameters();
        
        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('rapportId', $params[0]->getName());
        $this->assertEquals('auteurId', $params[1]->getName());
        $this->assertEquals('contenu', $params[2]->getName());
    }

    public function testMethodeEstCorrectionExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'estCorrection'));
    }

    public function testMethodeEstCommentaireExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'estCommentaire'));
    }

    public function testMethodeEstSuggestionExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'estSuggestion'));
    }

    public function testMethodeGetPositionExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'getPosition'));
    }

    public function testMethodeSetPositionExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'setPosition'));
    }

    public function testMethodeCompterParTypeExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'compterParType'));
        
        $reflection = new \ReflectionMethod(AnnotationRapport::class, 'compterParType');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeNombreAnnotationsExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'nombreAnnotations'));
    }

    public function testMethodeGetResumeExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'getResume'));
    }

    public function testMethodeSupprimerPourRapportExiste(): void
    {
        $this->assertTrue(method_exists(AnnotationRapport::class, 'supprimerPourRapport'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(AnnotationRapport::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new AnnotationRapport([]);
        $this->assertEquals('annotations_rapport', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(AnnotationRapport::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new AnnotationRapport([]);
        $this->assertEquals('id_annotation', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(AnnotationRapport::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new AnnotationRapport([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('rapport_id', $fillable);
        $this->assertContains('auteur_id', $fillable);
        $this->assertContains('contenu', $fillable);
        $this->assertContains('type_annotation', $fillable);
        $this->assertContains('page_numero', $fillable);
        $this->assertContains('position_json', $fillable);
    }
}
