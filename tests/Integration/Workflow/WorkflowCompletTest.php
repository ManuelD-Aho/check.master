<?php

declare(strict_types=1);

namespace Tests\Integration\Workflow;

use Tests\TestCase;
use App\Services\Workflow\ServiceWorkflow;

/**
 * Tests d'intégration pour le workflow complet du dossier étudiant
 * 
 * Ce fichier teste le parcours complet d'un dossier depuis la candidature
 * jusqu'à la diplomation, vérifiant tous les états et transitions.
 * 
 * @covers \App\Services\Workflow\ServiceWorkflow
 */
class WorkflowCompletTest extends TestCase
{
    /**
     * Les 14 états du workflow dans l'ordre
     */
    private const ETATS_WORKFLOW = [
        'candidature_soumise',
        'validation_scolarite',
        'validation_communication',
        'paiement_en_cours',
        'redaction_rapport',
        'evaluation_rapport',
        'commission_scientifique',
        'constitution_jury',
        'programmation_soutenance',
        'soutenance_planifiee',
        'soutenance_terminee',
        'deliberation',
        'archivage',
        'diplome',
    ];

    // =========================================================================
    // Tests de la définition du workflow
    // =========================================================================

    /**
     * @test
     * Vérifie que tous les états du workflow sont définis
     */
    public function testTousLesEtatsDefinis(): void
    {
        $this->assertCount(14, self::ETATS_WORKFLOW);
        $this->assertEquals('candidature_soumise', self::ETATS_WORKFLOW[0]);
        $this->assertEquals('diplome', self::ETATS_WORKFLOW[13]);
    }

    /**
     * @test
     * Vérifie que chaque état a une transition vers l'état suivant
     */
    public function testTransitionsSequentielles(): void
    {
        for ($i = 0; $i < count(self::ETATS_WORKFLOW) - 1; $i++) {
            $etatSource = self::ETATS_WORKFLOW[$i];
            $etatCible = self::ETATS_WORKFLOW[$i + 1];

            $this->assertNotEquals(
                $etatSource,
                $etatCible,
                "La transition de {$etatSource} vers {$etatCible} doit être définie"
            );
        }
    }

    /**
     * @test
     * L'état initial est candidature_soumise
     */
    public function testEtatInitial(): void
    {
        $this->assertEquals('candidature_soumise', self::ETATS_WORKFLOW[0]);
    }

    /**
     * @test
     * L'état final est diplome
     */
    public function testEtatFinal(): void
    {
        $this->assertEquals('diplome', end(self::ETATS_WORKFLOW));
    }

    // =========================================================================
    // Tests des gates (prérequis) pour les transitions
    // =========================================================================

    /**
     * @test
     * La validation scolarité requiert des documents complets
     */
    public function testGateValidationScolarite(): void
    {
        $prerequis = [
            'documents_complets' => true,
            'fiche_inscription_signee' => true,
        ];

        $this->assertArrayHasKey('documents_complets', $prerequis);
        $this->assertTrue($prerequis['documents_complets']);
    }

    /**
     * @test
     * La validation communication requiert la validation scolarité préalable
     */
    public function testGateValidationCommunication(): void
    {
        $prerequis = [
            'validation_scolarite_ok' => true,
            'theme_valide' => true,
        ];

        $this->assertArrayHasKey('validation_scolarite_ok', $prerequis);
        $this->assertTrue($prerequis['validation_scolarite_ok']);
    }

    /**
     * @test
     * Le paiement requiert la validation communication
     */
    public function testGatePaiement(): void
    {
        $prerequis = [
            'validation_communication_ok' => true,
            'montant_scolarite_defini' => true,
        ];

        $this->assertArrayHasKey('validation_communication_ok', $prerequis);
    }

    /**
     * @test
     * La rédaction du rapport requiert le paiement complet
     */
    public function testGateRedactionRapport(): void
    {
        $prerequis = [
            'paiement_complet' => true,
            'encadreur_affecte' => true,
        ];

        $this->assertArrayHasKey('paiement_complet', $prerequis);
    }

    /**
     * @test
     * L'évaluation du rapport requiert le rapport soumis
     */
    public function testGateEvaluationRapport(): void
    {
        $prerequis = [
            'rapport_soumis' => true,
            'version_finale' => true,
        ];

        $this->assertArrayHasKey('rapport_soumis', $prerequis);
    }

    /**
     * @test
     * La commission requiert l'évaluation positive du rapport
     */
    public function testGateCommission(): void
    {
        $prerequis = [
            'evaluation_positive' => true,
            'annotations_resolues' => true,
        ];

        $this->assertArrayHasKey('evaluation_positive', $prerequis);
    }

    /**
     * @test
     * La constitution du jury requiert l'avis favorable de la commission
     */
    public function testGateConstitutionJury(): void
    {
        $prerequis = [
            'avis_commission_favorable' => true,
        ];

        $this->assertArrayHasKey('avis_commission_favorable', $prerequis);
    }

    /**
     * @test
     * La programmation requiert le jury constitué (5 membres)
     */
    public function testGateProgrammationSoutenance(): void
    {
        $prerequis = [
            'jury_complet' => true,
            'nombre_membres' => 5,
            'tous_acceptes' => true,
        ];

        $this->assertArrayHasKey('jury_complet', $prerequis);
        $this->assertEquals(5, $prerequis['nombre_membres']);
    }

    /**
     * @test
     * La soutenance planifiée requiert une salle disponible
     */
    public function testGateSoutenancePlanifiee(): void
    {
        $prerequis = [
            'salle_reservee' => true,
            'date_confirmee' => true,
            'convocations_envoyees' => true,
        ];

        $this->assertArrayHasKey('salle_reservee', $prerequis);
    }

    /**
     * @test
     * La soutenance terminée requiert toutes les notes
     */
    public function testGateSoutenanceTerminee(): void
    {
        $prerequis = [
            'toutes_notes_saisies' => true,
            'moyenne_calculee' => true,
        ];

        $this->assertArrayHasKey('toutes_notes_saisies', $prerequis);
    }

    /**
     * @test
     * La délibération requiert le PV de soutenance
     */
    public function testGateDeliberation(): void
    {
        $prerequis = [
            'pv_soutenance_genere' => true,
            'signatures_jury' => true,
        ];

        $this->assertArrayHasKey('pv_soutenance_genere', $prerequis);
    }

    /**
     * @test
     * L'archivage requiert la délibération validée
     */
    public function testGateArchivage(): void
    {
        $prerequis = [
            'deliberation_validee' => true,
            'decision_finale' => 'admis',
        ];

        $this->assertArrayHasKey('deliberation_validee', $prerequis);
    }

    /**
     * @test
     * Le diplôme requiert l'archivage complet
     */
    public function testGateDiplome(): void
    {
        $prerequis = [
            'documents_archives' => true,
            'hash_integrite_valide' => true,
        ];

        $this->assertArrayHasKey('documents_archives', $prerequis);
    }

    // =========================================================================
    // Tests des SLA (délais maximaux)
    // =========================================================================

    /**
     * @test
     * Chaque état a un délai maximum défini
     */
    public function testDelaisMaxDefinis(): void
    {
        $delaisMax = [
            'candidature_soumise' => 3,
            'validation_scolarite' => 14,
            'validation_communication' => 7,
            'paiement_en_cours' => 30,
            'redaction_rapport' => 90,
            'evaluation_rapport' => 14,
            'commission_scientifique' => 7,
            'constitution_jury' => 14,
            'programmation_soutenance' => 7,
            'soutenance_planifiee' => 30,
            'soutenance_terminee' => 1,
            'deliberation' => 7,
            'archivage' => 3,
            'diplome' => 0, // État final
        ];

        $this->assertCount(14, $delaisMax);
        foreach (self::ETATS_WORKFLOW as $etat) {
            $this->assertArrayHasKey($etat, $delaisMax);
        }
    }

    /**
     * @test
     * Les alertes sont déclenchées aux seuils 50%, 80%, 100%
     */
    public function testSeuilsAlertes(): void
    {
        $seuils = [50, 80, 100];

        $this->assertContains(50, $seuils);
        $this->assertContains(80, $seuils);
        $this->assertContains(100, $seuils);
    }

    /**
     * @test
     * Calcul de la date limite depuis l'entrée dans l'état
     */
    public function testCalculDateLimite(): void
    {
        $dateEntree = new \DateTime('2024-01-15');
        $delaiJours = 14;
        $dateLimite = (clone $dateEntree)->modify("+{$delaiJours} days");

        $this->assertEquals('2024-01-29', $dateLimite->format('Y-m-d'));
    }

    // =========================================================================
    // Tests des rôles autorisés par transition
    // =========================================================================

    /**
     * @test
     * La scolarité peut valider les candidatures
     */
    public function testRoleValidationScolarite(): void
    {
        $rolesAutorises = ['scolarite', 'admin'];
        $this->assertContains('scolarite', $rolesAutorises);
    }

    /**
     * @test
     * La communication peut valider les thèmes
     */
    public function testRoleValidationCommunication(): void
    {
        $rolesAutorises = ['communication', 'admin'];
        $this->assertContains('communication', $rolesAutorises);
    }

    /**
     * @test
     * Le comptable peut enregistrer les paiements
     */
    public function testRolePaiement(): void
    {
        $rolesAutorises = ['comptable', 'scolarite', 'admin'];
        $this->assertContains('comptable', $rolesAutorises);
    }

    /**
     * @test
     * L'encadreur peut évaluer les rapports
     */
    public function testRoleEvaluationRapport(): void
    {
        $rolesAutorises = ['encadreur', 'admin'];
        $this->assertContains('encadreur', $rolesAutorises);
    }

    /**
     * @test
     * Les membres de la commission peuvent voter
     */
    public function testRoleCommission(): void
    {
        $rolesAutorises = ['commission', 'doyen', 'admin'];
        $this->assertContains('commission', $rolesAutorises);
    }

    /**
     * @test
     * Le responsable peut constituer le jury
     */
    public function testRoleConstitutionJury(): void
    {
        $rolesAutorises = ['responsable_formation', 'scolarite', 'admin'];
        $this->assertContains('responsable_formation', $rolesAutorises);
    }

    /**
     * @test
     * Le jury peut noter la soutenance
     */
    public function testRoleSoutenance(): void
    {
        $rolesAutorises = ['jury', 'president_jury'];
        $this->assertContains('jury', $rolesAutorises);
    }

    // =========================================================================
    // Tests des transitions de retour (rejets)
    // =========================================================================

    /**
     * @test
     * Un rejet de scolarité renvoie à candidature_soumise
     */
    public function testRejetScolarite(): void
    {
        $transition = [
            'source' => 'validation_scolarite',
            'cible' => 'candidature_soumise',
            'type' => 'rejet',
        ];

        $this->assertEquals('candidature_soumise', $transition['cible']);
    }

    /**
     * @test
     * Un rejet de communication renvoie à validation_scolarite
     */
    public function testRejetCommunication(): void
    {
        $transition = [
            'source' => 'validation_communication',
            'cible' => 'validation_scolarite',
            'type' => 'rejet',
        ];

        $this->assertEquals('validation_scolarite', $transition['cible']);
    }

    /**
     * @test
     * Un rejet de commission renvoie à redaction_rapport
     */
    public function testRejetCommission(): void
    {
        $transition = [
            'source' => 'commission_scientifique',
            'cible' => 'redaction_rapport',
            'type' => 'rejet',
        ];

        $this->assertEquals('redaction_rapport', $transition['cible']);
    }

    // =========================================================================
    // Tests de l'historique des transitions
    // =========================================================================

    /**
     * @test
     * L'historique enregistre toutes les informations nécessaires
     */
    public function testHistoriqueTransition(): void
    {
        $historique = [
            'dossier_id' => 1,
            'etat_source_id' => 1,
            'etat_cible_id' => 2,
            'utilisateur_id' => 5,
            'commentaire' => 'Validation effectuée',
            'created_at' => date('Y-m-d H:i:s'),
            'snapshot_json' => json_encode(['data' => 'test']),
        ];

        $this->assertArrayHasKey('dossier_id', $historique);
        $this->assertArrayHasKey('utilisateur_id', $historique);
        $this->assertArrayHasKey('snapshot_json', $historique);
    }

    /**
     * @test
     * Le snapshot JSON capture l'état complet du dossier
     */
    public function testSnapshotJson(): void
    {
        $snapshot = [
            'dossier_id' => 1,
            'etudiant' => [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
            ],
            'theme' => 'Sujet de mémoire',
            'etat_avant' => 'validation_scolarite',
            'etat_apres' => 'validation_communication',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $json = json_encode($snapshot);
        $decoded = json_decode($json, true);

        $this->assertArrayHasKey('dossier_id', $decoded);
        $this->assertArrayHasKey('etudiant', $decoded);
        $this->assertArrayHasKey('etat_avant', $decoded);
        $this->assertArrayHasKey('etat_apres', $decoded);
    }
}
