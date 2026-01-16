<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\NoteSoutenance;

class NoteSoutenanceTest extends TestCase
{
    public function testConstantesCoefficientsDefinies(): void
    {
        $this->assertEquals(0.40, NoteSoutenance::COEF_FOND);
        $this->assertEquals(0.30, NoteSoutenance::COEF_FORME);
        $this->assertEquals(0.30, NoteSoutenance::COEF_SOUTENANCE);
    }

    public function testSommeCoefficientsEgaleUn(): void
    {
        $somme = NoteSoutenance::COEF_FOND + NoteSoutenance::COEF_FORME + NoteSoutenance::COEF_SOUTENANCE;
        $this->assertEquals(1.0, $somme);
    }

    public function testNoteMaxDefinies(): void
    {
        $this->assertEquals(20.00, NoteSoutenance::NOTE_MAX);
    }

    public function testMethodeSoutenanceExiste(): void
    {
        $this->assertTrue(method_exists(NoteSoutenance::class, 'soutenance'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(NoteSoutenance::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new NoteSoutenance([]);
        $this->assertEquals('notes_soutenance', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(NoteSoutenance::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new NoteSoutenance([]);
        $this->assertEquals('id_note', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(NoteSoutenance::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new NoteSoutenance([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('soutenance_id', $fillable);
        $this->assertContains('membre_jury_id', $fillable);
        $this->assertContains('note_fond', $fillable);
        $this->assertContains('note_forme', $fillable);
        $this->assertContains('note_soutenance', $fillable);
        $this->assertContains('note_finale', $fillable);
        $this->assertContains('mention', $fillable);
    }
}
