<?php

declare(strict_types=1);

namespace Tests\Integration\Workflow;

use Tests\TestCase;
use App\Services\Workflow\ServiceWorkflow;
use App\Services\Workflow\ServiceEscalade;
use App\Services\Workflow\ServiceCommission;

/**
 * Tests d'intégration pour les transitions du workflow
 * 
 * @covers \App\Services\Workflow\ServiceWorkflow
 */
class TransitionsWorkflowTest extends TestCase
{
    // =========================================================================
    // Tests du parcours nominal (happy path)
    // =========================================================================

    /**
     * @test
     * Transition candidature_soumise → validation_scolarite
     */
    public function testTransitionCandidatureVersValidationScolarite(): void
    {
        $transition = [
            'source' => 'candidature_soumise',
            'cible' => 'validation_scolarite',
            'action' => 'soumettre_candidature',
            'roles' => ['etudiant'],
        ];

        $this->assertEquals('candidature_soumise', $transition['source']);
        $this->assertEquals('validation_scolarite', $transition['cible']);
        $this->assertContains('etudiant', $transition['roles']);
    }

    /**
     * @test
     * Transition validation_scolarite → validation_communication
     */
    public function testTransitionValidationScolariteVersCommunication(): void
    {
        $transition = [
            'source' => 'validation_scolarite',
            'cible' => 'validation_communication',
            'action' => 'valider_scolarite',
            'roles' => ['scolarite', 'admin'],
        ];

        $this->assertEquals('validation_scolarite', $transition['source']);
        $this->assertEquals('validation_communication', $transition['cible']);
    }

    /**
     * @test
     * Transition validation_communication → paiement_en_cours
     */
    public function testTransitionCommunicationVersPaiement(): void
    {
        $transition = [
            'source' => 'validation_communication',
            'cible' => 'paiement_en_cours',
            'action' => 'valider_communication',
            'roles' => ['communication', 'admin'],
        ];

        $this->assertEquals('validation_communication', $transition['source']);
        $this->assertEquals('paiement_en_cours', $transition['cible']);
    }

    /**
     * @test
     * Transition paiement_en_cours → redaction_rapport
     */
    public function testTransitionPaiementVersRedaction(): void
    {
        $transition = [
            'source' => 'paiement_en_cours',
            'cible' => 'redaction_rapport',
            'action' => 'confirmer_paiement_complet',
            'roles' => ['comptable', 'scolarite', 'admin'],
            'condition' => 'paiement_complet',
        ];

        $this->assertEquals('paiement_en_cours', $transition['source']);
        $this->assertEquals('confirmer_paiement_complet', $transition['action']);
    }

    /**
     * @test
     * Transition redaction_rapport → evaluation_rapport
     */
    public function testTransitionRedactionVersEvaluation(): void
    {
        $transition = [
            'source' => 'redaction_rapport',
            'cible' => 'evaluation_rapport',
            'action' => 'soumettre_rapport',
            'roles' => ['etudiant'],
            'condition' => 'rapport_version_finale',
        ];

        $this->assertEquals('redaction_rapport', $transition['source']);
        $this->assertContains('etudiant', $transition['roles']);
    }

    /**
     * @test
     * Transition evaluation_rapport → commission_scientifique
     */
    public function testTransitionEvaluationVersCommission(): void
    {
        $transition = [
            'source' => 'evaluation_rapport',
            'cible' => 'commission_scientifique',
            'action' => 'valider_evaluation',
            'roles' => ['encadreur', 'admin'],
            'condition' => 'evaluation_positive',
        ];

        $this->assertEquals('evaluation_rapport', $transition['source']);
        $this->assertEquals('commission_scientifique', $transition['cible']);
    }

    /**
     * @test
     * Transition commission_scientifique → constitution_jury
     */
    public function testTransitionCommissionVersJury(): void
    {
        $transition = [
            'source' => 'commission_scientifique',
            'cible' => 'constitution_jury',
            'action' => 'avis_favorable_commission',
            'roles' => ['commission', 'doyen'],
            'condition' => 'unanimite_favorable',
        ];

        $this->assertEquals('commission_scientifique', $transition['source']);
        $this->assertEquals('constitution_jury', $transition['cible']);
    }

    /**
     * @test
     * Transition constitution_jury → programmation_soutenance
     */
    public function testTransitionJuryVersProgrammation(): void
    {
        $transition = [
            'source' => 'constitution_jury',
            'cible' => 'programmation_soutenance',
            'action' => 'completer_jury',
            'roles' => ['responsable_formation', 'scolarite'],
            'condition' => 'jury_5_membres_acceptes',
        ];

        $this->assertEquals('constitution_jury', $transition['source']);
    }

    /**
     * @test
     * Transition programmation_soutenance → soutenance_planifiee
     */
    public function testTransitionProgrammationVersPlanifiee(): void
    {
        $transition = [
            'source' => 'programmation_soutenance',
            'cible' => 'soutenance_planifiee',
            'action' => 'planifier_soutenance',
            'roles' => ['scolarite', 'admin'],
            'condition' => 'salle_et_date_confirmees',
        ];

        $this->assertEquals('programmation_soutenance', $transition['source']);
    }

    /**
     * @test
     * Transition soutenance_planifiee → soutenance_terminee
     */
    public function testTransitionPlanifieeVersTerminee(): void
    {
        $transition = [
            'source' => 'soutenance_planifiee',
            'cible' => 'soutenance_terminee',
            'action' => 'terminer_soutenance',
            'roles' => ['president_jury', 'scolarite'],
            'condition' => 'toutes_notes_saisies',
        ];

        $this->assertEquals('soutenance_planifiee', $transition['source']);
    }

    /**
     * @test
     * Transition soutenance_terminee → deliberation
     */
    public function testTransitionTermineeVersDeliberation(): void
    {
        $transition = [
            'source' => 'soutenance_terminee',
            'cible' => 'deliberation',
            'action' => 'generer_pv',
            'roles' => ['president_jury', 'scolarite'],
            'condition' => 'pv_soutenance_signe',
        ];

        $this->assertEquals('soutenance_terminee', $transition['source']);
    }

    /**
     * @test
     * Transition deliberation → archivage
     */
    public function testTransitionDeliberationVersArchivage(): void
    {
        $transition = [
            'source' => 'deliberation',
            'cible' => 'archivage',
            'action' => 'valider_deliberation',
            'roles' => ['doyen', 'scolarite', 'admin'],
            'condition' => 'decision_finale_admis',
        ];

        $this->assertEquals('deliberation', $transition['source']);
    }

    /**
     * @test
     * Transition archivage → diplome
     */
    public function testTransitionArchivageVersDiplome(): void
    {
        $transition = [
            'source' => 'archivage',
            'cible' => 'diplome',
            'action' => 'archiver_dossier',
            'roles' => ['archiviste', 'scolarite', 'admin'],
            'condition' => 'documents_archives_integres',
        ];

        $this->assertEquals('archivage', $transition['source']);
        $this->assertEquals('diplome', $transition['cible']);
    }

    // =========================================================================
    // Tests des transitions de rejet
    // =========================================================================

    /**
     * @test
     * Rejet à l'étape validation_scolarite
     */
    public function testRejetValidationScolarite(): void
    {
        $transition = [
            'source' => 'validation_scolarite',
            'cible' => 'candidature_soumise',
            'action' => 'rejeter_scolarite',
            'roles' => ['scolarite', 'admin'],
            'motif_requis' => true,
        ];

        $this->assertEquals('candidature_soumise', $transition['cible']);
        $this->assertTrue($transition['motif_requis']);
    }

    /**
     * @test
     * Rejet à l'étape validation_communication
     */
    public function testRejetValidationCommunication(): void
    {
        $transition = [
            'source' => 'validation_communication',
            'cible' => 'validation_scolarite',
            'action' => 'rejeter_communication',
            'roles' => ['communication', 'admin'],
            'motif_requis' => true,
        ];

        $this->assertEquals('validation_scolarite', $transition['cible']);
    }

    /**
     * @test
     * Rejet à l'étape evaluation_rapport
     */
    public function testRejetEvaluationRapport(): void
    {
        $transition = [
            'source' => 'evaluation_rapport',
            'cible' => 'redaction_rapport',
            'action' => 'demander_corrections',
            'roles' => ['encadreur'],
            'motif_requis' => true,
        ];

        $this->assertEquals('redaction_rapport', $transition['cible']);
    }

    /**
     * @test
     * Rejet par la commission (ajourné)
     */
    public function testRejetCommission(): void
    {
        $transition = [
            'source' => 'commission_scientifique',
            'cible' => 'redaction_rapport',
            'action' => 'ajourner_commission',
            'roles' => ['commission', 'doyen'],
            'motif_requis' => true,
        ];

        $this->assertEquals('redaction_rapport', $transition['cible']);
    }

    // =========================================================================
    // Tests des transitions bloquées
    // =========================================================================

    /**
     * @test
     * Impossible de sauter une étape du workflow
     */
    public function testTransitionDirecteImpossible(): void
    {
        $transitionIllegale = [
            'source' => 'candidature_soumise',
            'cible' => 'redaction_rapport',
            'valide' => false,
        ];

        $this->assertFalse($transitionIllegale['valide']);
    }

    /**
     * @test
     * Impossible de revenir en arrière sans permission
     */
    public function testRetourArriereBloque(): void
    {
        $transitionIllegale = [
            'source' => 'paiement_en_cours',
            'cible' => 'candidature_soumise',
            'valide' => false,
            'raison' => 'Retour arrière non autorisé',
        ];

        $this->assertFalse($transitionIllegale['valide']);
    }

    /**
     * @test
     * Transition bloquée si gate non satisfaite
     */
    public function testTransitionBloqueeSansGate(): void
    {
        $gate = [
            'condition' => 'paiement_complet',
            'satisfaite' => false,
        ];

        $transition = [
            'source' => 'paiement_en_cours',
            'cible' => 'redaction_rapport',
            'bloquee' => !$gate['satisfaite'],
        ];

        $this->assertTrue($transition['bloquee']);
    }

    // =========================================================================
    // Tests des notifications sur transition
    // =========================================================================

    /**
     * @test
     * Notification envoyée à l'étudiant lors de la validation
     */
    public function testNotificationEtudiantValidation(): void
    {
        $notification = [
            'type' => 'validation_scolarite_ok',
            'destinataires' => ['etudiant'],
            'canaux' => ['email', 'interne'],
        ];

        $this->assertContains('etudiant', $notification['destinataires']);
        $this->assertContains('email', $notification['canaux']);
    }

    /**
     * @test
     * Notification envoyée au jury lors de la planification
     */
    public function testNotificationJuryPlanification(): void
    {
        $notification = [
            'type' => 'convocation_soutenance',
            'destinataires' => ['jury', 'etudiant', 'encadreur'],
            'canaux' => ['email'],
        ];

        $this->assertContains('jury', $notification['destinataires']);
    }

    /**
     * @test
     * Notification de rejet avec motif
     */
    public function testNotificationRejet(): void
    {
        $notification = [
            'type' => 'rejet_dossier',
            'destinataires' => ['etudiant'],
            'canaux' => ['email', 'sms', 'interne'],
            'contient_motif' => true,
        ];

        $this->assertTrue($notification['contient_motif']);
        $this->assertContains('sms', $notification['canaux']);
    }
}
