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
        $code = CodeTemporaire::genererCode();
        $this->assertEquals(8, strlen($code));
    }

    public function testGenererCodeLongueurPersonnalisee(): void
    {
        $code = CodeTemporaire::genererCode(12);
        $this->assertEquals(12, strlen($code));
    }

    public function testGenererCodeCaracteresValides(): void
    {
        $code = CodeTemporaire::genererCode(100);
        $this->assertMatchesRegularExpression('/^[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]+$/', $code);
    }

    public function testMethodeHasherCodeExiste(): void
    {
        $this->assertTrue(method_exists(CodeTemporaire::class, 'hasherCode'));
    }

    public function testHasherCodeRetourneHash(): void
    {
        $code = 'TESTCODE';
        $hash = CodeTemporaire::hasherCode($code);
        
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($code, $hash);
        $this->assertTrue(password_verify($code, $hash));
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
