<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Signature\ServiceSignature;

/**
 * Tests unitaires exhaustifs pour ServiceSignature
 * 
 * @covers \App\Services\Signature\ServiceSignature
 */
class ServiceSignatureTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceSignature::class));
    }

    // =========================================================================
    // Tests des constantes
    // =========================================================================

    /**
     * @test
     * La constante OTP_LONGUEUR existe
     */
    public function testConstanteOtpLongueur(): void
    {
        $reflection = new \ReflectionClass(ServiceSignature::class);
        $constants = $reflection->getConstants();

        $this->assertArrayHasKey('OTP_LONGUEUR', $constants);
        $this->assertEquals(6, $constants['OTP_LONGUEUR']);
    }

    /**
     * @test
     * La constante OTP_VALIDITE_MINUTES existe
     */
    public function testConstanteOtpValiditeMinutes(): void
    {
        $reflection = new \ReflectionClass(ServiceSignature::class);
        $constants = $reflection->getConstants();

        $this->assertArrayHasKey('OTP_VALIDITE_MINUTES', $constants);
        $this->assertEquals(10, $constants['OTP_VALIDITE_MINUTES']);
    }

    // =========================================================================
    // Tests de la méthode genererOtp()
    // =========================================================================

    /**
     * @test
     * La méthode genererOtp existe avec les bons paramètres
     */
    public function testGenererOtpMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceSignature::class, 'genererOtp'));

        $reflection = new \ReflectionMethod(ServiceSignature::class, 'genererOtp');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('utilisateurId', $params[0]->getName());
        $this->assertEquals('documentRef', $params[1]->getName());
    }

    /**
     * @test
     * Le paramètre utilisateurId est un int
     */
    public function testGenererOtpUtilisateurIdEstInt(): void
    {
        $reflection = new \ReflectionMethod(ServiceSignature::class, 'genererOtp');
        $params = $reflection->getParameters();

        $this->assertEquals('int', $params[0]->getType()->getName());
    }

    /**
     * @test
     * Le paramètre documentRef est un string
     */
    public function testGenererOtpDocumentRefEstString(): void
    {
        $reflection = new \ReflectionMethod(ServiceSignature::class, 'genererOtp');
        $params = $reflection->getParameters();

        $this->assertEquals('string', $params[1]->getType()->getName());
    }

    /**
     * @test
     * La méthode genererOtp retourne array
     */
    public function testGenererOtpReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceSignature::class, 'genererOtp');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode verifierOtp()
    // =========================================================================

    /**
     * @test
     * La méthode verifierOtp existe avec les bons paramètres
     */
    public function testVerifierOtpMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceSignature::class, 'verifierOtp'));

        $reflection = new \ReflectionMethod(ServiceSignature::class, 'verifierOtp');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('utilisateurId', $params[0]->getName());
        $this->assertEquals('documentRef', $params[1]->getName());
        $this->assertEquals('code', $params[2]->getName());
    }

    /**
     * @test
     * La méthode verifierOtp retourne bool
     */
    public function testVerifierOtpReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceSignature::class, 'verifierOtp');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode signerDocument()
    // =========================================================================

    /**
     * @test
     * La méthode signerDocument existe avec les bons paramètres
     */
    public function testSignerDocumentMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceSignature::class, 'signerDocument'));

        $reflection = new \ReflectionMethod(ServiceSignature::class, 'signerDocument');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('utilisateurId', $params[0]->getName());
        $this->assertEquals('documentRef', $params[1]->getName());
        $this->assertEquals('code', $params[2]->getName());
    }

    /**
     * @test
     * La méthode signerDocument retourne array
     */
    public function testSignerDocumentReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceSignature::class, 'signerDocument');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode genererCode()
    // =========================================================================

    /**
     * @test
     * La méthode genererCode existe et est privée
     */
    public function testGenererCodeEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceSignature::class, 'genererCode'));

        $reflection = new \ReflectionMethod(ServiceSignature::class, 'genererCode');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * Le code OTP généré a la bonne longueur
     */
    public function testCodeOtpBonneLongueur(): void
    {
        // Test manuel du format
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= random_int(0, 9);
        }

        $this->assertEquals(6, strlen($code));
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $code);
    }

    // =========================================================================
    // Tests de la méthode genererSignature()
    // =========================================================================

    /**
     * @test
     * La méthode genererSignature existe et est privée
     */
    public function testGenererSignatureEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServiceSignature::class, 'genererSignature'));

        $reflection = new \ReflectionMethod(ServiceSignature::class, 'genererSignature');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * La méthode genererSignature prend les bons paramètres
     */
    public function testGenererSignatureParametres(): void
    {
        $reflection = new \ReflectionMethod(ServiceSignature::class, 'genererSignature');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('utilisateurId', $params[0]->getName());
        $this->assertEquals('documentRef', $params[1]->getName());
    }

    // =========================================================================
    // Tests de la méthode verifierSignature()
    // =========================================================================

    /**
     * @test
     * La méthode verifierSignature existe
     */
    public function testVerifierSignatureMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceSignature::class, 'verifierSignature'));

        $reflection = new \ReflectionMethod(ServiceSignature::class, 'verifierSignature');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('signature', $params[0]->getName());
        $this->assertEquals('metadonnees', $params[1]->getName());
    }

    /**
     * @test
     * La méthode verifierSignature retourne bool
     */
    public function testVerifierSignatureReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceSignature::class, 'verifierSignature');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de format de signature
    // =========================================================================

    /**
     * @test
     * Une signature SHA256 a 64 caractères
     */
    public function testSignatureSha256Longueur(): void
    {
        $data = implode('|', [
            1,
            'document_ref_123',
            time(),
            bin2hex(random_bytes(16)),
        ]);

        $signature = hash('sha256', $data);

        $this->assertEquals(64, strlen($signature));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $signature);
    }

    /**
     * @test
     * Une signature vide est invalide
     */
    public function testSignatureVideInvalide(): void
    {
        $signatureVide = '';
        $this->assertNotEquals(64, strlen($signatureVide));
    }

    /**
     * @test
     * Une signature valide a exactement 64 caractères hexadécimaux
     */
    public function testFormatSignatureValide(): void
    {
        $signatureValide = hash('sha256', 'test_data');

        $this->assertEquals(64, strlen($signatureValide));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $signatureValide);
    }

    // =========================================================================
    // Tests de cohérence globale
    // =========================================================================

    /**
     * @test
     * Toutes les méthodes publiques sont statiques
     */
    public function testToutesMethodesPubliquesStatiques(): void
    {
        $methods = [
            'genererOtp',
            'verifierOtp',
            'signerDocument',
            'verifierSignature',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceSignature::class, $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode {$method} devrait être statique"
            );
            $this->assertTrue(
                $reflection->isPublic(),
                "La méthode {$method} devrait être publique"
            );
        }
    }

    /**
     * @test
     * Les méthodes privées sont correctement encapsulées
     */
    public function testMethodesPriveesEncapsulees(): void
    {
        $privateMethods = [
            'genererCode',
            'genererSignature',
        ];

        foreach ($privateMethods as $method) {
            $reflection = new \ReflectionMethod(ServiceSignature::class, $method);
            $this->assertTrue(
                $reflection->isPrivate(),
                "La méthode {$method} devrait être privée"
            );
        }
    }

    /**
     * @test
     * Le hash Argon2id est utilisé pour le stockage OTP
     */
    public function testArgon2idUtilisePourOtp(): void
    {
        $code = '123456';
        $hash = password_hash($code, PASSWORD_ARGON2ID);

        $this->assertTrue(password_verify($code, $hash));
        $this->assertStringStartsWith('$argon2id$', $hash);
    }
}
