<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Exoneration;

class ExonerationTest extends TestCase
{
    public function testConstantesMotifsDefinies(): void
    {
        $this->assertEquals('Boursier', Exoneration::MOTIF_BOURSE);
        $this->assertEquals('Mérite', Exoneration::MOTIF_MERITE);
        $this->assertEquals('Social', Exoneration::MOTIF_SOCIAL);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Exoneration::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Exoneration([]);
        $this->assertEquals('exonerations', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Exoneration::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Exoneration([]);
        $this->assertEquals('id_exoneration', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Exoneration::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Exoneration([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('etudiant_id', $fillable);
        $this->assertContains('annee_acad_id', $fillable);
        $this->assertContains('montant', $fillable);
        $this->assertContains('motif', $fillable);
        $this->assertContains('statut', $fillable);
    }
}
