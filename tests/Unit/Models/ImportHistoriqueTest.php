<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\ImportHistorique;

class ImportHistoriqueTest extends TestCase
{
    public function testConstantesTypesDefinies(): void
    {
        $this->assertEquals('etudiants', ImportHistorique::TYPE_ETUDIANTS);
        $this->assertEquals('enseignants', ImportHistorique::TYPE_ENSEIGNANTS);
        $this->assertEquals('notes', ImportHistorique::TYPE_NOTES);
        $this->assertEquals('paiements', ImportHistorique::TYPE_PAIEMENTS);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(ImportHistorique::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new ImportHistorique([]);
        $this->assertEquals('imports_historiques', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(ImportHistorique::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new ImportHistorique([]);
        $this->assertEquals('id_import', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(ImportHistorique::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new ImportHistorique([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('type_import', $fillable);
        $this->assertContains('fichier_nom', $fillable);
        $this->assertContains('nb_lignes_totales', $fillable);
        $this->assertContains('nb_lignes_reussies', $fillable);
        $this->assertContains('nb_lignes_erreurs', $fillable);
    }
}
