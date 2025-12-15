<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Security\ServiceAuthentification;

/**
 * Tests unitaires pour ServiceAuthentification
 */
class ServiceAuthentificationTest extends TestCase
{
    private ServiceAuthentification $service;

    protected function setUp(): void
    {
        $this->service = new ServiceAuthentification();
    }

    /**
     * Test du hashage de mot de passe avec Argon2id
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
     */
    public function testVerificationMotDePasseCorrecte(): void
    {
        $password = 'MonMotDePasse123!';
        $hash = $this->service->hasherMotDePasse($password);

        $this->assertTrue($this->service->verifierMotDePasse($password, $hash));
    }

    /**
     * Test de la vérification de mot de passe incorrecte
     */
    public function testVerificationMotDePasseIncorrecte(): void
    {
        $password = 'MonMotDePasse123!';
        $hash = $this->service->hasherMotDePasse($password);

        $this->assertFalse($this->service->verifierMotDePasse('MauvaisMotDePasse', $hash));
    }

    /**
     * Test de la génération de mot de passe temporaire
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
     */
    public function testGenerationMotDePasseTemporaireLongueurPersonnalisee(): void
    {
        $password = $this->service->genererMotDePasseTemporaire(16);
        $this->assertEquals(16, strlen($password));
    }

    /**
     * Test que chaque hash est unique (salt différent)
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
}
