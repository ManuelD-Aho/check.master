<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Archive;

class ArchiveTest extends TestCase
{
    public function testMethodeDocumentExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'document'));
    }

    public function testMethodeNonVerifieesExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'nonVerifiees'));
        
        $reflection = new \ReflectionMethod(Archive::class, 'nonVerifiees');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeVerrouilleesExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'verrouillees'));
        
        $reflection = new \ReflectionMethod(Archive::class, 'verrouillees');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeAVerifierExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'aVerifier'));
    }

    public function testMethodeCreerDepuisDocumentExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'creerDepuisDocument'));
        
        $reflection = new \ReflectionMethod(Archive::class, 'creerDepuisDocument');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeVerifierIntegriteExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'verifierIntegrite'));
    }

    public function testMethodeVerrouillerExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'verrouiller'));
    }

    public function testMethodeDeverrouillerExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'deverrouiller'));
    }

    public function testMethodeEstIntegreExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'estIntegre'));
    }

    public function testMethodeEstVerrouilleeExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'estVerrouillee'));
    }

    public function testMethodeVerifierToutesArchivesExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'verifierToutesArchives'));
        
        $reflection = new \ReflectionMethod(Archive::class, 'verifierToutesArchives');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeStatistiquesExiste(): void
    {
        $this->assertTrue(method_exists(Archive::class, 'statistiques'));
        
        $reflection = new \ReflectionMethod(Archive::class, 'statistiques');
        $this->assertTrue($reflection->isStatic());
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Archive::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Archive([]);
        $this->assertEquals('archives', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Archive::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Archive([]);
        $this->assertEquals('id_archive', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Archive::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Archive([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('document_id', $fillable);
        $this->assertContains('hash_sha256', $fillable);
        $this->assertContains('verifie', $fillable);
        $this->assertContains('derniere_verification', $fillable);
        $this->assertContains('verrouille', $fillable);
    }
}
