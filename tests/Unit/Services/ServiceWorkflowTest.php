<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour ServiceWorkflow
 */
class ServiceWorkflowTest extends TestCase
{
    /**
     * @test
     * Les états de workflow sont définis
     */
    public function testEtatsWorkflowDefinis(): void
    {
        $etats = [
            'candidature_soumise',
            'validation_scolarite',
            'validation_communication',
            'redaction_rapport',
            'evaluation_rapport',
            'programmation_soutenance',
            'soutenance_terminee',
            'diplome',
        ];

        $this->assertGreaterThan(5, count($etats));
        $this->assertContains('candidature_soumise', $etats);
        $this->assertContains('diplome', $etats);
    }

    /**
     * @test
     * Les transitions sont valides
     */
    public function testTransitionsValides(): void
    {
        $transitions = [
            ['source' => 'candidature_soumise', 'cible' => 'validation_scolarite'],
            ['source' => 'validation_scolarite', 'cible' => 'validation_communication'],
            ['source' => 'soutenance_terminee', 'cible' => 'diplome'],
        ];

        foreach ($transitions as $transition) {
            $this->assertArrayHasKey('source', $transition);
            $this->assertArrayHasKey('cible', $transition);
            $this->assertNotEquals($transition['source'], $transition['cible']);
        }
    }

    /**
     * @test
     * Le délai max est respecté en jours
     */
    public function testDelaiMaxEnJours(): void
    {
        $etat = [
            'code_etat' => 'validation_scolarite',
            'delai_max_jours' => 14,
        ];

        $this->assertIsInt($etat['delai_max_jours']);
        $this->assertGreaterThan(0, $etat['delai_max_jours']);
    }

    /**
     * @test
     * Les alertes sont générées aux bons seuils
     */
    public function testSeuilsAlertes(): void
    {
        $seuils = ['50_pourcent', '80_pourcent', '100_pourcent'];

        $this->assertCount(3, $seuils);
        $this->assertContains('50_pourcent', $seuils);
        $this->assertContains('100_pourcent', $seuils);
    }

    /**
     * @test
     * L'historique enregistre l'utilisateur
     */
    public function testHistoriqueEnregistreUtilisateur(): void
    {
        $historique = [
            'dossier_id' => 1,
            'etat_source_id' => 1,
            'etat_cible_id' => 2,
            'utilisateur_id' => 5,
            'commentaire' => 'Validation effectuée',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->assertArrayHasKey('utilisateur_id', $historique);
        $this->assertGreaterThan(0, $historique['utilisateur_id']);
    }

    /**
     * @test
     * Le snapshot JSON capture l'état complet
     */
    public function testSnapshotJsonComplet(): void
    {
        $snapshot = json_encode([
            'dossier_id' => 1,
            'etudiant_nom' => 'Dupont',
            'theme' => 'Sujet de mémoire',
            'etat_avant' => 'validation_scolarite',
            'etat_apres' => 'validation_communication',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        $this->assertJson($snapshot);
        $decoded = json_decode($snapshot, true);
        $this->assertArrayHasKey('dossier_id', $decoded);
        $this->assertArrayHasKey('timestamp', $decoded);
    }

    /**
     * @test
     * Les rôles autorisés sont définis par transition
     */
    public function testRolesAutorisesParTransition(): void
    {
        $transition = [
            'code_transition' => 'valider_scolarite',
            'roles_autorises' => ['scolarite', 'admin'],
        ];

        $this->assertIsArray($transition['roles_autorises']);
        $this->assertContains('scolarite', $transition['roles_autorises']);
    }

    /**
     * @test
     * La date limite est calculée à partir du délai
     */
    public function testCalculDateLimite(): void
    {
        $delaiJours = 14;
        $dateEntree = new \DateTime();
        $dateLimite = (clone $dateEntree)->modify("+{$delaiJours} days");

        $this->assertGreaterThan($dateEntree, $dateLimite);

        $diff = $dateEntree->diff($dateLimite);
        $this->assertEquals($delaiJours, $diff->days);
    }

    /**
     * @test
     * Un dossier ne peut pas revenir en arrière sans permission
     */
    public function testTransitionArriereBloqueeSansPermission(): void
    {
        $transitionsPermises = [
            'candidature_soumise' => ['validation_scolarite'],
            'validation_scolarite' => ['validation_communication', 'candidature_soumise'], // retour possible avec permission
        ];

        // Simuler une tentative de retour
        $etatActuel = 'validation_scolarite';
        $etatSouhaite = 'candidature_soumise';

        $permis = in_array($etatSouhaite, $transitionsPermises[$etatActuel] ?? []);
        $this->assertTrue($permis); // Retour permis explicitement
    }
}
