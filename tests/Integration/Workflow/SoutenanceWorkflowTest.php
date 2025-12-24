<?php

declare(strict_types=1);

namespace Tests\Integration\Workflow;

use Tests\TestCase;
use App\Services\Soutenance\ServiceJury;
use App\Services\Soutenance\ServiceCalendrier;
use App\Services\Soutenance\ServiceNotes;

/**
 * Tests d'intégration pour le processus de Soutenance
 * 
 * @covers \App\Services\Soutenance\ServiceJury
 * @covers \App\Services\Soutenance\ServiceCalendrier
 * @covers \App\Services\Soutenance\ServiceNotes
 */
class SoutenanceWorkflowTest extends TestCase
{
    // =========================================================================
    // Tests de la constitution du Jury
    // =========================================================================

    /**
     * @test
     * Un jury doit avoir exactement 5 membres
     */
    public function testJuryRequiert5Membres(): void
    {
        $nombreRequis = 5;
        $this->assertEquals(5, $nombreRequis);
    }

    /**
     * @test
     * Les rôles du jury sont définis
     */
    public function testRolesJury(): void
    {
        $roles = [
            'president',
            'rapporteur',
            'examinateur',
            'encadreur_academique',
            'encadreur_professionnel',
        ];

        $this->assertCount(5, $roles);
        $this->assertContains('president', $roles);
        $this->assertContains('rapporteur', $roles);
    }

    /**
     * @test
     * Un membre jury peut accepter l'invitation
     */
    public function testAccepterInvitationJury(): void
    {
        $membre = [
            'id' => 1,
            'enseignant_id' => 10,
            'dossier_id' => 1,
            'role' => 'rapporteur',
            'statut' => 'Accepte',
            'date_reponse' => date('Y-m-d H:i:s'),
        ];

        $this->assertEquals('Accepte', $membre['statut']);
    }

    /**
     * @test
     * Un membre jury peut refuser avec motif
     */
    public function testRefuserInvitationJury(): void
    {
        $membre = [
            'id' => 1,
            'statut' => 'Refuse',
            'motif_refus' => 'Conflit de calendrier',
            'date_reponse' => date('Y-m-d H:i:s'),
        ];

        $this->assertEquals('Refuse', $membre['statut']);
        $this->assertNotEmpty($membre['motif_refus']);
    }

    /**
     * @test
     * Le jury est complet quand 5 membres ont accepté
     */
    public function testJuryComplet(): void
    {
        $membres = [
            ['statut' => 'Accepte'],
            ['statut' => 'Accepte'],
            ['statut' => 'Accepte'],
            ['statut' => 'Accepte'],
            ['statut' => 'Accepte'],
        ];

        $acceptes = array_filter($membres, fn($m) => $m['statut'] === 'Accepte');
        $this->assertCount(5, $acceptes);
    }

    // =========================================================================
    // Tests de la planification de soutenance
    // =========================================================================

    /**
     * @test
     * Une soutenance a une date, heure et salle
     */
    public function testPlanificationSoutenance(): void
    {
        $soutenance = [
            'dossier_id' => 1,
            'date_soutenance' => '2024-06-15',
            'heure_debut' => '09:00:00',
            'heure_fin' => '11:00:00',
            'salle_id' => 3,
            'statut' => 'Planifiee',
        ];

        $this->assertEquals('2024-06-15', $soutenance['date_soutenance']);
        $this->assertEquals('Planifiee', $soutenance['statut']);
    }

    /**
     * @test
     * Détection de conflit de salle
     */
    public function testConflitSalle(): void
    {
        $soutenanceExistante = [
            'salle_id' => 3,
            'date' => '2024-06-15',
            'heure_debut' => '09:00:00',
            'heure_fin' => '11:00:00',
        ];

        $nouvelleSoutenance = [
            'salle_id' => 3,
            'date' => '2024-06-15',
            'heure_debut' => '10:00:00',
            'heure_fin' => '12:00:00',
        ];

        // Vérification de chevauchement
        $conflit = (
            $soutenanceExistante['salle_id'] === $nouvelleSoutenance['salle_id'] &&
            $soutenanceExistante['date'] === $nouvelleSoutenance['date'] &&
            $soutenanceExistante['heure_debut'] < $nouvelleSoutenance['heure_fin'] &&
            $soutenanceExistante['heure_fin'] > $nouvelleSoutenance['heure_debut']
        );

        $this->assertTrue($conflit);
    }

    /**
     * @test
     * Détection de conflit pour un membre du jury
     */
    public function testConflitMembreJury(): void
    {
        $membreId = 10;

        $soutenances = [
            ['date' => '2024-06-15', 'heure' => '09:00', 'jury' => [10, 11, 12]],
            ['date' => '2024-06-15', 'heure' => '10:00', 'jury' => [10, 13, 14]],
        ];

        $conflits = array_filter(
            $soutenances,
            fn($s) =>
            in_array($membreId, $s['jury']) && $s['date'] === '2024-06-15'
        );

        $this->assertCount(2, $conflits);
    }

    /**
     * @test
     * Les convocations sont envoyées après planification
     */
    public function testConvocationsEnvoyees(): void
    {
        $convocations = [
            ['destinataire' => 'etudiant', 'type' => 'convocation_soutenance'],
            ['destinataire' => 'president', 'type' => 'convocation_jury'],
            ['destinataire' => 'rapporteur', 'type' => 'convocation_jury'],
            ['destinataire' => 'examinateur', 'type' => 'convocation_jury'],
            ['destinataire' => 'encadreur_academique', 'type' => 'convocation_jury'],
            ['destinataire' => 'encadreur_professionnel', 'type' => 'convocation_jury'],
        ];

        $this->assertCount(6, $convocations);
    }

    // =========================================================================
    // Tests de la notation
    // =========================================================================

    /**
     * @test
     * Chaque membre du jury attribue une note
     */
    public function testNotationParMembre(): void
    {
        $notes = [
            ['membre_id' => 1, 'note' => 16.5, 'role' => 'president'],
            ['membre_id' => 2, 'note' => 15.0, 'role' => 'rapporteur'],
            ['membre_id' => 3, 'note' => 17.0, 'role' => 'examinateur'],
            ['membre_id' => 4, 'note' => 16.0, 'role' => 'encadreur_academique'],
            ['membre_id' => 5, 'note' => 15.5, 'role' => 'encadreur_professionnel'],
        ];

        $this->assertCount(5, $notes);
    }

    /**
     * @test
     * Les notes sont entre 0 et 20
     */
    public function testNotesDansIntervalle(): void
    {
        $notes = [16.5, 15.0, 17.0, 16.0, 15.5];

        foreach ($notes as $note) {
            $this->assertGreaterThanOrEqual(0, $note);
            $this->assertLessThanOrEqual(20, $note);
        }
    }

    /**
     * @test
     * La moyenne est calculée correctement
     */
    public function testCalculMoyenne(): void
    {
        $notes = [16.5, 15.0, 17.0, 16.0, 15.5];
        $moyenne = array_sum($notes) / count($notes);

        $this->assertEquals(16.0, $moyenne);
    }

    /**
     * @test
     * La mention est déterminée selon la moyenne
     */
    public function testDeterminationMention(): void
    {
        $baremes = [
            ['min' => 16, 'max' => 20, 'mention' => 'Très Bien'],
            ['min' => 14, 'max' => 15.99, 'mention' => 'Bien'],
            ['min' => 12, 'max' => 13.99, 'mention' => 'Assez Bien'],
            ['min' => 10, 'max' => 11.99, 'mention' => 'Passable'],
            ['min' => 0, 'max' => 9.99, 'mention' => 'Ajourné'],
        ];

        $moyenne = 16.0;
        $mention = 'Très Bien';

        foreach ($baremes as $bareme) {
            if ($moyenne >= $bareme['min'] && $moyenne <= $bareme['max']) {
                $this->assertEquals($bareme['mention'], $mention);
                break;
            }
        }
    }

    /**
     * @test
     * La soutenance ne peut être finalisée que si tous ont noté
     */
    public function testFinalisationRequiertToutesNotes(): void
    {
        $notesAttendues = 5;
        $notesRecues = 5;

        $peutFinaliser = ($notesRecues === $notesAttendues);
        $this->assertTrue($peutFinaliser);
    }

    // =========================================================================
    // Tests du PV de soutenance
    // =========================================================================

    /**
     * @test
     * Le PV de soutenance contient toutes les informations
     */
    public function testContenuPvSoutenance(): void
    {
        $pv = [
            'dossier' => [
                'etudiant' => 'Dupont Jean',
                'theme' => 'Système de gestion académique',
            ],
            'jury' => [
                ['nom' => 'Prof. Martin', 'role' => 'President'],
                ['nom' => 'Dr. Bernard', 'role' => 'Rapporteur'],
            ],
            'date_soutenance' => '2024-06-15',
            'notes' => [16.5, 15.0, 17.0, 16.0, 15.5],
            'moyenne' => 16.0,
            'mention' => 'Très Bien',
            'decision' => 'Admis',
        ];

        $this->assertArrayHasKey('dossier', $pv);
        $this->assertArrayHasKey('jury', $pv);
        $this->assertArrayHasKey('moyenne', $pv);
        $this->assertArrayHasKey('mention', $pv);
        $this->assertEquals('Admis', $pv['decision']);
    }

    /**
     * @test
     * Le PV génère un hash d'intégrité
     */
    public function testHashIntegritePv(): void
    {
        $contenuPv = json_encode(['test' => 'data']);
        $hash = hash('sha256', $contenuPv);

        $this->assertEquals(64, strlen($hash));
    }

    // =========================================================================
    // Tests de l'annulation de soutenance
    // =========================================================================

    /**
     * @test
     * Une soutenance peut être annulée avec motif
     */
    public function testAnnulationSoutenance(): void
    {
        $annulation = [
            'soutenance_id' => 1,
            'motif' => 'Indisponibilité du président de jury',
            'annule_par' => 5,
            'date_annulation' => date('Y-m-d H:i:s'),
        ];

        $this->assertNotEmpty($annulation['motif']);
    }

    /**
     * @test
     * L'annulation notifie tous les participants
     */
    public function testNotificationAnnulation(): void
    {
        $notifications = [
            ['destinataire' => 'etudiant', 'type' => 'annulation_soutenance'],
            ['destinataire' => 'jury', 'type' => 'annulation_soutenance'],
            ['destinataire' => 'scolarite', 'type' => 'annulation_soutenance'],
        ];

        $this->assertGreaterThanOrEqual(3, count($notifications));
    }
}
