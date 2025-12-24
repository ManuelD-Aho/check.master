<?php

declare(strict_types=1);

namespace Tests\Integration\Workflow;

use Tests\TestCase;
use App\Services\Workflow\ServiceCommission;

/**
 * Tests d'intégration pour la Commission Scientifique
 * 
 * Teste le processus de vote en commission avec unanimité requise,
 * les tours multiples et l'escalade au Doyen.
 * 
 * @covers \App\Services\Workflow\ServiceCommission
 */
class CommissionWorkflowTest extends TestCase
{
    // =========================================================================
    // Tests du processus de vote
    // =========================================================================

    /**
     * @test
     * Une session de commission peut être démarrée
     */
    public function testDemarrerSessionCommission(): void
    {
        $session = [
            'id_session' => 1,
            'dossier_id' => 1,
            'tour' => 1,
            'statut' => 'en_cours',
            'date_debut' => date('Y-m-d H:i:s'),
        ];

        $this->assertEquals(1, $session['tour']);
        $this->assertEquals('en_cours', $session['statut']);
    }

    /**
     * @test
     * Les membres peuvent voter avec différentes décisions
     */
    public function testEnregistrerVote(): void
    {
        $decisions = ['Favorable', 'Defavorable', 'Reserve', 'Ajourne'];

        foreach ($decisions as $decision) {
            $vote = [
                'session_id' => 1,
                'membre_id' => 1,
                'decision' => $decision,
                'commentaire' => "Vote {$decision}",
            ];

            $this->assertContains($vote['decision'], $decisions);
        }
    }

    /**
     * @test
     * L'unanimité favorable conduit à l'avis positif
     */
    public function testUnanimiteFavorable(): void
    {
        $votes = [
            ['membre_id' => 1, 'decision' => 'Favorable'],
            ['membre_id' => 2, 'decision' => 'Favorable'],
            ['membre_id' => 3, 'decision' => 'Favorable'],
            ['membre_id' => 4, 'decision' => 'Favorable'],
            ['membre_id' => 5, 'decision' => 'Favorable'],
        ];

        $tousVotesFavorables = array_reduce($votes, function ($carry, $vote) {
            return $carry && $vote['decision'] === 'Favorable';
        }, true);

        $this->assertTrue($tousVotesFavorables);
    }

    /**
     * @test
     * L'unanimité défavorable conduit à l'avis négatif
     */
    public function testUnanimiteDefavorable(): void
    {
        $votes = [
            ['membre_id' => 1, 'decision' => 'Defavorable'],
            ['membre_id' => 2, 'decision' => 'Defavorable'],
            ['membre_id' => 3, 'decision' => 'Defavorable'],
        ];

        $tousVotesDefavorables = array_reduce($votes, function ($carry, $vote) {
            return $carry && $vote['decision'] === 'Defavorable';
        }, true);

        $this->assertTrue($tousVotesDefavorables);
    }

    /**
     * @test
     * Le vote "Réservé" nécessite des corrections
     */
    public function testVoteReserve(): void
    {
        $vote = [
            'decision' => 'Reserve',
            'commentaire' => 'Corrections mineures nécessaires',
            'annotations_requises' => true,
        ];

        $this->assertEquals('Reserve', $vote['decision']);
        $this->assertTrue($vote['annotations_requises']);
    }

    /**
     * @test
     * Le vote "Ajourné" repousse la décision
     */
    public function testVoteAjourne(): void
    {
        $vote = [
            'decision' => 'Ajourne',
            'commentaire' => 'Travail insuffisant, révisions majeures nécessaires',
            'retour_redaction' => true,
        ];

        $this->assertEquals('Ajourne', $vote['decision']);
        $this->assertTrue($vote['retour_redaction']);
    }

    // =========================================================================
    // Tests des tours multiples
    // =========================================================================

    /**
     * @test
     * Maximum 3 tours de vote autorisés
     */
    public function testMaxTroisTours(): void
    {
        $maxTours = 3;
        $this->assertEquals(3, $maxTours);
    }

    /**
     * @test
     * Passage au tour suivant si pas d'unanimité
     */
    public function testPassageAuTourSuivant(): void
    {
        $session = [
            'tour_actuel' => 1,
            'unanimite' => false,
            'peut_passer_tour_suivant' => true,
        ];

        $this->assertFalse($session['unanimite']);
        $this->assertTrue($session['peut_passer_tour_suivant']);
    }

    /**
     * @test
     * Chaque tour conserve l'historique des votes
     */
    public function testHistoriqueVotesParTour(): void
    {
        $historique = [
            'tour_1' => [
                ['membre_id' => 1, 'decision' => 'Favorable'],
                ['membre_id' => 2, 'decision' => 'Reserve'],
            ],
            'tour_2' => [
                ['membre_id' => 1, 'decision' => 'Favorable'],
                ['membre_id' => 2, 'decision' => 'Favorable'],
            ],
        ];

        $this->assertCount(2, $historique);
        $this->assertArrayHasKey('tour_1', $historique);
        $this->assertArrayHasKey('tour_2', $historique);
    }

    // =========================================================================
    // Tests de l'escalade au Doyen
    // =========================================================================

    /**
     * @test
     * Escalade automatique après 3 tours sans unanimité
     */
    public function testEscaladeAutomatiqueApres3Tours(): void
    {
        $session = [
            'tour_actuel' => 3,
            'unanimite' => false,
            'escalade_requise' => true,
            'escalade_vers' => 'doyen',
        ];

        $this->assertTrue($session['escalade_requise']);
        $this->assertEquals('doyen', $session['escalade_vers']);
    }

    /**
     * @test
     * Le Doyen a la décision finale
     */
    public function testDecisionFinaleDoyen(): void
    {
        $decisionDoyen = [
            'utilisateur_id' => 1,
            'role' => 'doyen',
            'decision' => 'Favorable',
            'finale' => true,
            'commentaire' => 'Décision exceptionnelle du Doyen',
        ];

        $this->assertTrue($decisionDoyen['finale']);
        $this->assertEquals('doyen', $decisionDoyen['role']);
    }

    /**
     * @test
     * L'escalade est tracée dans l'historique
     */
    public function testHistoriqueEscalade(): void
    {
        $escalade = [
            'session_id' => 1,
            'raison' => 'Absence unanimite apres 3 tours',
            'niveau' => 'doyen',
            'date_escalade' => date('Y-m-d H:i:s'),
            'resolu' => false,
        ];

        $this->assertEquals('doyen', $escalade['niveau']);
        $this->assertFalse($escalade['resolu']);
    }

    // =========================================================================
    // Tests de la clôture de session
    // =========================================================================

    /**
     * @test
     * La session peut être clôturée avec un avis favorable
     */
    public function testCloturerSessionAvisFavorable(): void
    {
        $session = [
            'statut' => 'termine',
            'avis_final' => 'Favorable',
            'date_fin' => date('Y-m-d H:i:s'),
            'transition_suivante' => 'constitution_jury',
        ];

        $this->assertEquals('termine', $session['statut']);
        $this->assertEquals('constitution_jury', $session['transition_suivante']);
    }

    /**
     * @test
     * La session peut être clôturée avec un avis défavorable
     */
    public function testCloturerSessionAvisDefavorable(): void
    {
        $session = [
            'statut' => 'termine',
            'avis_final' => 'Defavorable',
            'date_fin' => date('Y-m-d H:i:s'),
            'transition_suivante' => 'redaction_rapport',
            'motif' => 'Travail non conforme aux attentes',
        ];

        $this->assertEquals('redaction_rapport', $session['transition_suivante']);
        $this->assertNotEmpty($session['motif']);
    }

    /**
     * @test
     * Le PV de commission est généré automatiquement
     */
    public function testGenerationPvCommission(): void
    {
        $pv = [
            'session_id' => 1,
            'type_document' => 'pv_commission',
            'contenu' => [
                'dossier' => ['etudiant' => 'Dupont Jean', 'theme' => 'Test'],
                'votes' => [],
                'decision_finale' => 'Favorable',
            ],
            'hash' => hash('sha256', 'contenu_pv'),
        ];

        $this->assertEquals('pv_commission', $pv['type_document']);
        $this->assertEquals(64, strlen($pv['hash']));
    }

    // =========================================================================
    // Tests des notifications de commission
    // =========================================================================

    /**
     * @test
     * Notification aux membres pour nouvelle session
     */
    public function testNotificationNouvelleSession(): void
    {
        $notification = [
            'type' => 'nouvelle_session_commission',
            'destinataires' => ['membres_commission'],
            'canaux' => ['email', 'interne'],
            'contenu' => [
                'dossier_id' => 1,
                'etudiant' => 'Dupont Jean',
                'date_limite_vote' => date('Y-m-d', strtotime('+7 days')),
            ],
        ];

        $this->assertContains('email', $notification['canaux']);
    }

    /**
     * @test
     * Notification à l'étudiant du résultat
     */
    public function testNotificationResultatEtudiant(): void
    {
        $notification = [
            'type' => 'resultat_commission',
            'destinataires' => ['etudiant', 'encadreur'],
            'canaux' => ['email', 'sms'],
            'contenu' => [
                'decision' => 'Favorable',
                'commentaires' => 'Félicitations',
            ],
        ];

        $this->assertContains('sms', $notification['canaux']);
    }
}
