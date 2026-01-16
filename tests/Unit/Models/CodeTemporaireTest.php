<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\CodeTemporaire;

class CodeTemporaireTest extends TestCase
{
    public function testConstantesTypesDefinies(): void
    {
        $this->assertEquals('president_jury', CodeTemporaire::TYPE_PRESIDENT_JURY);
        $this->assertEquals('reset_password', CodeTemporaire::TYPE_RESET_PASSWORD);
        $this->assertEquals('verification', CodeTemporaire::TYPE_VERIFICATION);
    }

    public function testMethodeUtilisateurExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'utilisateur'));
    }

    public function testMethodeSoutenanceExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'soutenance'));
    }

    public function testMethodeFindByHashExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'findByHash'));
        
        $reflection = new \ReflectionMethod(CodeTemporaire::class, 'findByHash');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeEstValideExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'estValide'));
    }

    public function testMethodeEstUtiliseExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'estUtilise'));
    }

    public function testMethodeEstExpireExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'estExpire'));
    }

    public function testMethodeGenererCodeExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'genererCode'));
        
        $reflection = new \ReflectionMethod(CodeTemporaire::class, 'genererCode');
        $this->assertTrue($reflection->isStatic());
    }

    public function testGenererCodeLongueurParDefaut(): void
    {
        $reflection = new \ReflectionMethod(CodeTemporaire::class, 'genererCode');
        $params = $reflection->getParameters();
        
        $this->assertTrue($params[0]->isOptional());
        $this->assertEquals(8, $params[0]->getDefaultValue());
    }

    public function testGenererCodeLongueurParametre(): void
    {
        $reflection = new \ReflectionMethod(CodeTemporaire::class, 'genererCode');
        $params = $reflection->getParameters();
        
        $this->assertCount(1, $params);
        $this->assertEquals('longueur', $params[0]->getName());
    }

    public function testGenererCodeEstStatiqueEtPublique(): void
    {
        $reflection = new \ReflectionMethod(CodeTemporaire::class, 'genererCode');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testMethodeHasherCodeExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'hasherCode'));
        
        $reflection = new \ReflectionMethod(CodeTemporaire::class, 'hasherCode');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testHasherCodeAccepteString(): void
    {
        $reflection = new \ReflectionMethod(CodeTemporaire::class, 'hasherCode');
        $params = $reflection->getParameters();
        
        $this->assertCount(1, $params);
        $this->assertEquals('code', $params[0]->getName());
    }

    public function testMethodeVerifierCodeExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'verifierCode'));
    }

    public function testMethodeCreerPourPresidentJuryExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'creerPourPresidentJury'));
    }

    public function testMethodeCreerPourResetPasswordExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'creerPourResetPassword'));
    }

    public function testMethodeCreerPourVerificationExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'creerPourVerification'));
    }

    public function testMethodeMarquerUtiliseExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'marquerUtilise'));
    }

    public function testMethodeNettoyerExpiresExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'nettoyerExpires'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(CodeTemporaire::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new CodeTemporaire([]);
        $this->assertEquals('codes_temporaires', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(CodeTemporaire::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new CodeTemporaire([]);
        $this->assertEquals('id_code', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(CodeTemporaire::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new CodeTemporaire([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('soutenance_id', $fillable);
        $this->assertContains('code_hash', $fillable);
        $this->assertContains('type', $fillable);
        $this->assertContains('valide_de', $fillable);
        $this->assertContains('valide_jusqu_a', $fillable);
        $this->assertContains('utilise', $fillable);
    }
}
