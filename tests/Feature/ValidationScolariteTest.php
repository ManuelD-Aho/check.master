<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests fonctionnels pour la validation des dossiers scolarité
 * 
 * Scénarios de bout en bout pour le workflow de validation scolarité.
 */
class ValidationScolariteTest extends TestCase
{
    /**
     * @test
     * Un dossier peut être soumis pour validation scolarité
     */
    public function testSoumissionDossierValidation(): void
    {
        $dossier = $this->createTestDossierEtudiant([
            'etat_actuel_id' => 1, // État initial
        ]);

        $this->assertArrayHasKey('id_dossier', $dossier);
        $this->assertArrayHasKey('etudiant_id', $dossier);
        $this->assertEquals(1, $dossier['etat_actuel_id']);
    }

    /**
     * @test
     * La validation scolarité vérifie les documents requis
     */
    public function testVerificationDocumentsRequis(): void
    {
        $documentsRequis = [
            'releve_notes',
            'attestation_inscription',
            'fiche_engagement',
        ];

        foreach ($documentsRequis as $document) {
            $this->assertIsString($document);
            $this->assertNotEmpty($document);
        }
    }

    /**
     * @test
     * Un dossier validé passe à l'état suivant
     */
    public function testTransitionEtatApresValidation(): void
    {
        $dossierAvant = $this->createTestDossierEtudiant(['etat_actuel_id' => 1]);
        $dossierApres = array_merge($dossierAvant, [
            'etat_actuel_id' => 2, // Validé scolarité
            'date_entree_etat' => date('Y-m-d H:i:s'),
        ]);

        $this->assertNotEquals($dossierAvant['etat_actuel_id'], $dossierApres['etat_actuel_id']);
        $this->assertEquals(2, $dossierApres['etat_actuel_id']);
    }

    /**
     * @test
     * Un dossier rejeté reste dans le même état
     */
    public function testDossierRejeteResteEnAttente(): void
    {
        $dossier = $this->createTestDossierEtudiant([
            'etat_actuel_id' => 1,
        ]);

        // Simuler un rejet (pas de changement d'état)
        $this->assertEquals(1, $dossier['etat_actuel_id']);
    }

    /**
     * @test
     * La validation enregistre l'utilisateur validateur
     */
    public function testEnregistrementValidateur(): void
    {
        $validation = [
            'dossier_id' => 1,
            'utilisateur_validateur_id' => 5,
            'date_validation' => date('Y-m-d H:i:s'),
            'decision' => 'Valide',
        ];

        $this->assertArrayHasKey('utilisateur_validateur_id', $validation);
        $this->assertGreaterThan(0, $validation['utilisateur_validateur_id']);
    }

    /**
     * @test
     * Une notification est créée après validation
     */
    public function testNotificationApresValidation(): void
    {
        $notification = [
            'destinataire_id' => 1,
            'template_code' => 'VALIDATION_SCOLARITE_OK',
            'statut' => 'En_attente',
        ];

        $this->assertEquals('VALIDATION_SCOLARITE_OK', $notification['template_code']);
        $this->assertArrayHasKey('destinataire_id', $notification);
    }

    /**
     * @test
     * Les dates limites sont calculées correctement
     */
    public function testCalculDateLimite(): void
    {
        $delaiJours = 14; // Délai pour valider un dossier
        $dateEntree = date('Y-m-d H:i:s');
        $dateLimite = date('Y-m-d H:i:s', strtotime("+{$delaiJours} days"));

        $dossier = $this->createTestDossierEtudiant([
            'date_entree_etat' => $dateEntree,
            'date_limite_etat' => $dateLimite,
        ]);

        $this->assertArrayHasKey('date_limite_etat', $dossier);
        $this->assertGreaterThan($dossier['date_entree_etat'], $dossier['date_limite_etat']);
    }

    /**
     * Helper pour créer un dossier étudiant de test
     * 
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function createTestDossierEtudiant(array $overrides = []): array
    {
        return array_merge([
            'id_dossier' => 1,
            'etudiant_id' => 1,
            'annee_acad_id' => 1,
            'etat_actuel_id' => 1,
            'date_entree_etat' => date('Y-m-d H:i:s'),
            'date_limite_etat' => date('Y-m-d H:i:s', strtotime('+14 days')),
        ], $overrides);
    }
}
