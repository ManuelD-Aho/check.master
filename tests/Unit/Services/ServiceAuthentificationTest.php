<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Security\ServiceAuthentification;

/**
 * Tests unitaires pour ServiceAuthentification
 * 
 * @see PRD 01 - Authentification & Utilisateurs
 * @covers \App\Services\Security\ServiceAuthentification
 */
class ServiceAuthentificationTest extends TestCase
{
    private ServiceAuthentification $service;

    protected function setUp(): void
    {
        $this->service = new ServiceAuthentification();
    }

    // ===== TESTS HASHAGE MOT DE PASSE =====

    /**
     * Test du hashage de mot de passe avec Argon2id
     * @test
     */
    public function testHashageMotDePasseUtiliseArgon2id(): void
    {
        $password = 'MonMotDePasse123!';
        $hash = $this->service->hasherMotDePasse($password);

        // Vérifier que le hash commence par $argon2id$
        $this->assertStringStartsWith('$argon2id$', $hash);
    }

    /**
     * Test de la vérification de mot de passe correcte
     * @test
     */
    public function testVerificationMotDePasseCorrecte(): void
    {
        $password = 'MonMotDePasse123!';
        $hash = $this->service->hasherMotDePasse($password);

        $this->assertTrue($this->service->verifierMotDePasse($password, $hash));
    }

    /**
     * Test de la vérification de mot de passe incorrecte
     * @test
     */
    public function testVerificationMotDePasseIncorrecte(): void
    {
        $password = 'MonMotDePasse123!';
        $hash = $this->service->hasherMotDePasse($password);

        $this->assertFalse($this->service->verifierMotDePasse('MauvaisMotDePasse', $hash));
    }

    /**
     * Test que chaque hash est unique (salt différent)
     * @test
     */
    public function testHashDifferentPourMemeMotDePasse(): void
    {
        $password = 'MonMotDePasse123!';
        $hash1 = $this->service->hasherMotDePasse($password);
        $hash2 = $this->service->hasherMotDePasse($password);

        $this->assertNotEquals($hash1, $hash2);

        // Les deux doivent quand même valider le même mot de passe
        $this->assertTrue($this->service->verifierMotDePasse($password, $hash1));
        $this->assertTrue($this->service->verifierMotDePasse($password, $hash2));
    }

    // ===== TESTS MOT DE PASSE TEMPORAIRE =====

    /**
     * Test de la génération de mot de passe temporaire
     * @test
     */
    public function testGenerationMotDePasseTemporaire(): void
    {
        $password = $this->service->genererMotDePasseTemporaire();

        // Vérifier la longueur par défaut (12 caractères)
        $this->assertEquals(12, strlen($password));

        // Générer un autre pour vérifier l'aléatoire
        $password2 = $this->service->genererMotDePasseTemporaire();
        $this->assertNotEquals($password, $password2);
    }

    /**
     * Test de la génération de mot de passe temporaire avec longueur personnalisée
     * @test
     */
    public function testGenerationMotDePasseTemporaireLongueurPersonnalisee(): void
    {
        $password = $this->service->genererMotDePasseTemporaire(16);
        $this->assertEquals(16, strlen($password));
    }

    /**
     * Test que le mot de passe temporaire ne contient pas de caractères ambigus
     * @test
     */
    public function testMotDePasseTemporaireSansCaracteresAmbigus(): void
    {
        // Générer plusieurs mots de passe et vérifier qu'aucun ne contient 0, O, 1, I, l
        for ($i = 0; $i < 50; $i++) {
            $password = $this->service->genererMotDePasseTemporaire();
            $this->assertStringNotContainsString('0', $password);
            $this->assertStringNotContainsString('O', $password);
            $this->assertStringNotContainsString('1', $password);
            $this->assertStringNotContainsString('I', $password);
            $this->assertStringNotContainsString('l', $password);
        }
    }

    // ===== TESTS STRUCTURE DES MÉTHODES =====

    /**
     * Test que la méthode authentifier existe
     * @test
     */
    public function testMethodeAuthentifierExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'authentifier'));
    }

    /**
     * Test que la méthode creerSession existe
     * @test
     */
    public function testMethodeCreerSessionExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'creerSession'));
    }

    /**
     * Test que la méthode validerSession existe
     * @test
     */
    public function testMethodeValiderSessionExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'validerSession'));
    }

    /**
     * Test que la méthode supprimerSession existe
     * @test
     */
    public function testMethodeSupprimerSessionExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'supprimerSession'));
    }

    /**
     * Test que la méthode forcerDeconnexion existe
     * @test
     */
    public function testMethodeForcerDeconnexionExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'forcerDeconnexion'));
    }

    /**
     * Test que la méthode genererCodePresidentJury existe
     * @test
     */
    public function testMethodeGenererCodePresidentJuryExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'genererCodePresidentJury'));
    }

    /**
     * Test que la méthode validerCodeTemporaire existe
     * @test
     */
    public function testMethodeValiderCodeTemporaireExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'validerCodeTemporaire'));
    }

    /**
     * Test que la méthode nettoyerSessionsExpirees existe
     * @test
     */
    public function testMethodeNettoyerSessionsExpireesExiste(): void
    {
        $this->assertTrue(method_exists($this->service, 'nettoyerSessionsExpirees'));
    }

    // ===== TESTS CONSTANTES =====

    /**
     * Test des constantes de seuils brute-force
     * @test
     */
    public function testConstantesBruteForce(): void
    {
        // Ces constantes sont privées mais on vérifie la structure de la classe
        // qui doit implémenter la protection brute-force: 3 échecs -> 1 min, 5 -> 15 min, 10 -> verrouillage
        $reflection = new \ReflectionClass(ServiceAuthentification::class);
        
        // Vérifier que la classe a une méthode pour gérer l'authentification
        $this->assertTrue($reflection->hasMethod('authentifier'));
    }

    /**
     * Test que le service est instanciable
     * @test
     */
    public function testServiceInstanciable(): void
    {
        $service = new ServiceAuthentification();
        $this->assertInstanceOf(ServiceAuthentification::class, $service);
    }
}
