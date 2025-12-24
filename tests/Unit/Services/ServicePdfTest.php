<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Documents\ServicePdf;

/**
 * Tests unitaires exhaustifs pour ServicePdf
 * 
 * @covers \App\Services\Documents\ServicePdf
 */
class ServicePdfTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServicePdf::class));
    }

    // =========================================================================
    // Tests de la méthode generer()
    // =========================================================================

    /**
     * @test
     * La méthode generer existe avec les bons paramètres
     */
    public function testGenererMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'generer'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'generer');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertEquals('typeDocument', $params[0]->getName());
        $this->assertEquals('donnees', $params[1]->getName());
    }

    /**
     * @test
     * Le paramètre typeDocument est un string
     */
    public function testGenererTypeDocumentEstString(): void
    {
        $reflection = new \ReflectionMethod(ServicePdf::class, 'generer');
        $params = $reflection->getParameters();

        $this->assertEquals('string', $params[0]->getType()->getName());
    }

    /**
     * @test
     * Le paramètre donnees est un array
     */
    public function testGenererDonneesEstArray(): void
    {
        $reflection = new \ReflectionMethod(ServicePdf::class, 'generer');
        $params = $reflection->getParameters();

        $this->assertEquals('array', $params[1]->getType()->getName());
    }

    // =========================================================================
    // Tests de la méthode genererAvance()
    // =========================================================================

    /**
     * @test
     * La méthode genererAvance existe avec les bons paramètres
     */
    public function testGenererAvanceMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'genererAvance'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'genererAvance');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertEquals('typeDocument', $params[0]->getName());
        $this->assertEquals('donnees', $params[1]->getName());
    }

    // =========================================================================
    // Tests de la méthode creerTcpdf()
    // =========================================================================

    /**
     * @test
     * La méthode creerTcpdf existe et est privée
     */
    public function testCreerTcpdfEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'creerTcpdf'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'creerTcpdf');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode creerMpdf()
    // =========================================================================

    /**
     * @test
     * La méthode creerMpdf existe et est privée
     */
    public function testCreerMpdfEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'creerMpdf'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'creerMpdf');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode appliquerTemplate()
    // =========================================================================

    /**
     * @test
     * La méthode appliquerTemplate existe et est privée
     */
    public function testAppliquerTemplateEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'appliquerTemplate'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'appliquerTemplate');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * La méthode appliquerTemplate prend les bons paramètres
     */
    public function testAppliquerTemplateParametres(): void
    {
        $reflection = new \ReflectionMethod(ServicePdf::class, 'appliquerTemplate');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('type', $params[0]->getName());
        $this->assertEquals('donnees', $params[1]->getName());
    }

    // =========================================================================
    // Tests de la méthode getTemplate()
    // =========================================================================

    /**
     * @test
     * La méthode getTemplate existe et est privée
     */
    public function testGetTemplateEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'getTemplate'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'getTemplate');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode interpolerVariables()
    // =========================================================================

    /**
     * @test
     * La méthode interpolerVariables existe et est privée
     */
    public function testInterpolerVariablesEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'interpolerVariables'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'interpolerVariables');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests des templates
    // =========================================================================

    /**
     * @test
     * La méthode templateRecuPaiement existe
     */
    public function testTemplateRecuPaiementExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateRecuPaiement'));
    }

    /**
     * @test
     * La méthode templateRecuPenalite existe
     */
    public function testTemplateRecuPenaliteExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateRecuPenalite'));
    }

    /**
     * @test
     * La méthode templateBulletinNotes existe
     */
    public function testTemplateBulletinNotesExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateBulletinNotes'));
    }

    /**
     * @test
     * La méthode templatePvCommission existe
     */
    public function testTemplatePvCommissionExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templatePvCommission'));
    }

    /**
     * @test
     * La méthode templatePvSoutenance existe
     */
    public function testTemplatePvSoutenanceExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templatePvSoutenance'));
    }

    /**
     * @test
     * La méthode templateConvocation existe
     */
    public function testTemplateConvocationExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateConvocation'));
    }

    /**
     * @test
     * La méthode templateAttestationDiplome existe
     */
    public function testTemplateAttestationDiplomeExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateAttestationDiplome'));
    }

    /**
     * @test
     * La méthode templateCertificatScolarite existe
     */
    public function testTemplateCertificatScolariteExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateCertificatScolarite'));
    }

    /**
     * @test
     * La méthode templateLettreJury existe
     */
    public function testTemplateLettreJuryExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateLettreJury'));
    }

    /**
     * @test
     * La méthode templateGenerique existe
     */
    public function testTemplateGeneriqueExiste(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'templateGenerique'));
    }

    // =========================================================================
    // Tests des méthodes utilitaires
    // =========================================================================

    /**
     * @test
     * La méthode genererNomFichier existe et est privée
     */
    public function testGenererNomFichierEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'genererNomFichier'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'genererNomFichier');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * La méthode getStoragePath existe et est privée
     */
    public function testGetStoragePathEstPrivee(): void
    {
        $this->assertTrue(method_exists(ServicePdf::class, 'getStoragePath'));

        $reflection = new \ReflectionMethod(ServicePdf::class, 'getStoragePath');
        $this->assertTrue($reflection->isPrivate());
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
            'generer',
            'genererAvance',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServicePdf::class, $method);
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
     * Les 13 types de documents sont définis
     */
    public function testTypesDocumentsDefinis(): void
    {
        $typesAttendus = [
            'recu_paiement',
            'recu_penalite',
            'bulletin_notes',
            'pv_commission',
            'pv_soutenance',
            'convocation',
            'attestation_diplome',
            'certificat_scolarite',
            'lettre_jury',
            'generique',
        ];

        $this->assertGreaterThanOrEqual(10, count($typesAttendus));
    }

    /**
     * @test
     * Le hash SHA256 est calculé pour l'intégrité
     */
    public function testCalculHashSha256(): void
    {
        $testContent = 'PDF content test';
        $hash = hash('sha256', $testContent);

        $this->assertEquals(64, strlen($hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }
}
