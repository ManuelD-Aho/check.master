<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\DocumentGenere;

class DocumentGenereTest extends TestCase
{
    public function testConstantesTypesDocumentsDefinies(): void
    {
        $this->assertEquals('recu_paiement', DocumentGenere::TYPE_RECU_PAIEMENT);
        $this->assertEquals('recu_penalite', DocumentGenere::TYPE_RECU_PENALITE);
        $this->assertEquals('bulletin_notes', DocumentGenere::TYPE_BULLETIN_NOTES);
        $this->assertEquals('pv_commission', DocumentGenere::TYPE_PV_COMMISSION);
        $this->assertEquals('pv_soutenance', DocumentGenere::TYPE_PV_SOUTENANCE);
        $this->assertEquals('convocation', DocumentGenere::TYPE_CONVOCATION);
        $this->assertEquals('attestation_diplome', DocumentGenere::TYPE_ATTESTATION_DIPLOME);
        $this->assertEquals('rapport_evaluation', DocumentGenere::TYPE_RAPPORT_EVALUATION);
        $this->assertEquals('bulletin_provisoire', DocumentGenere::TYPE_BULLETIN_PROVISOIRE);
        $this->assertEquals('certificat_scolarite', DocumentGenere::TYPE_CERTIFICAT_SCOLARITE);
        $this->assertEquals('lettre_jury', DocumentGenere::TYPE_LETTRE_JURY);
        $this->assertEquals('attestation_stage', DocumentGenere::TYPE_ATTESTATION_STAGE);
        $this->assertEquals('bordereau_transmission', DocumentGenere::TYPE_BORDEREAU_TRANSMISSION);
    }

    public function testNombreTotalTypesDocuments(): void
    {
        $reflection = new \ReflectionClass(DocumentGenere::class);
        $constants = $reflection->getConstants();
        
        $typesDocuments = array_filter(array_keys($constants), function($key) {
            return strpos($key, 'TYPE_') === 0;
        });
        
        $this->assertCount(13, $typesDocuments);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(DocumentGenere::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new DocumentGenere([]);
        $this->assertEquals('documents_generes', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(DocumentGenere::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new DocumentGenere([]);
        $this->assertEquals('id_document', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(DocumentGenere::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new DocumentGenere([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('type_document', $fillable);
        $this->assertContains('entite_type', $fillable);
        $this->assertContains('entite_id', $fillable);
        $this->assertContains('chemin_fichier', $fillable);
        $this->assertContains('nom_fichier', $fillable);
        $this->assertContains('hash_sha256', $fillable);
    }
}
