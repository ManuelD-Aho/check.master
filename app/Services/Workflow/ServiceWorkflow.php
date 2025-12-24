<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Models\DossierEtudiant;
use App\Models\WorkflowEtat;
use App\Models\WorkflowTransition;
use App\Models\WorkflowHistorique;
use App\Models\WorkflowAlerte;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\WorkflowException;
use Src\Exceptions\ForbiddenException;
use Src\Exceptions\NotFoundException;
use App\Orm\Model;

/**
 * Service Workflow
 * 
 * Gère la machine à états pour les 14 états du workflow de suivi des dossiers.
 * Implémente les gates, SLA/délais et génération de snapshots JSON.
 * 
 * @see PRD Section Workflow
 * @see Constitution II - Workflow-Driven Logic
 */
class ServiceWorkflow
{
    /**
     * Seuils d'alerte SLA (pourcentage du délai écoulé)
     */
    private const SLA_SEUIL_JAUNE = 50;  // 50% du délai
    private const SLA_SEUIL_ORANGE = 80; // 80% du délai
    private const SLA_SEUIL_ROUGE = 100; // 100% du délai (dépassé)

    /**
     * Effectue une transition d'état pour un dossier
     *
     * @throws WorkflowException Si la transition n'est pas autorisée
     * @throws NotFoundException Si le dossier n'existe pas
     * @throws ForbiddenException Si l'utilisateur n'a pas les droits
     */
    public function effectuerTransition(
        int $dossierId,
        string $codeEtatCible,
        int $utilisateurId,
        ?string $commentaire = null
    ): bool {
        // Charger le dossier
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            throw new NotFoundException('Dossier non trouvé');
        }

        // Récupérer l'état actuel
        $etatActuel = $dossier->getEtatActuel();
        if ($etatActuel === null) {
            throw new WorkflowException('État actuel du dossier non défini');
        }

        // Récupérer l'état cible
        $etatCible = WorkflowEtat::findByCode($codeEtatCible);
        if ($etatCible === null) {
            throw WorkflowException::invalidTransition(
                $etatActuel->code_etat,
                $codeEtatCible,
                'État cible inexistant'
            );
        }

        // Vérifier si déjà dans cet état
        if ($etatActuel->code_etat === $codeEtatCible) {
            throw WorkflowException::alreadyInState($codeEtatCible);
        }

        // Vérifier si état terminal
        if ($etatActuel->estTerminal()) {
            throw WorkflowException::terminalState($etatActuel->code_etat);
        }

        // Vérifier si la transition est autorisée
        $transition = WorkflowTransition::trouverTransition(
            $etatActuel->getId(),
            $etatCible->getId()
        );

        if ($transition === null) {
            throw WorkflowException::invalidTransition(
                $etatActuel->code_etat,
                $codeEtatCible
            );
        }

        // Vérifier les gates (prérequis)
        $this->verifierGates($dossier, $etatCible);

        // Créer le snapshot avant transition
        $snapshotAvant = $this->genererSnapshot($dossier);

        // Effectuer la transition dans une transaction
        Model::beginTransaction();
        try {
            // Calculer la nouvelle date limite si délai défini
            $dateLimite = null;
            if ($etatCible->delai_max_jours) {
                $dateLimite = date('Y-m-d H:i:s', time() + ($etatCible->delai_max_jours * 86400));
            }

            // Mettre à jour le dossier
            $dossier->etat_actuel_id = $etatCible->getId();
            $dossier->date_entree_etat = date('Y-m-d H:i:s');
            $dossier->date_limite_etat = $dateLimite;
            $dossier->save();

            // Enregistrer dans l'historique
            WorkflowHistorique::enregistrer(
                $dossierId,
                $etatActuel->getId(),
                $etatCible->getId(),
                $transition->getId(),
                $utilisateurId,
                $commentaire,
                $snapshotAvant
            );

            // Log d'audit
            ServiceAudit::log('transition_workflow', 'dossier', $dossierId, [
                'etat_source' => $etatActuel->code_etat,
                'etat_cible' => $etatCible->code_etat,
                'transition' => $transition->code_transition,
                'commentaire' => $commentaire,
            ]);

            // Envoyer les notifications si configurées
            if ($transition->doitNotifier()) {
                $this->envoyerNotificationsTransition($dossier, $etatActuel, $etatCible);
            }

            Model::commit();
            return true;
        } catch (\Exception $e) {
            Model::rollBack();
            throw $e;
        }
    }

    /**
     * Vérifie les gates (prérequis) pour une transition
     *
     * @throws WorkflowException Si un prérequis n'est pas rempli
     */
    private function verifierGates(DossierEtudiant $dossier, WorkflowEtat $etatCible): void
    {
        $codeEtat = $etatCible->code_etat;

        // Gate: Candidature validée avant rapport
        if (in_array($codeEtat, [
            WorkflowEtat::ETAT_FILTRE_COMMUNICATION,
            WorkflowEtat::ETAT_EN_ATTENTE_COMMISSION,
        ], true)) {
            $candidature = $dossier->getCandidature();
            if ($candidature === null || $candidature->statut !== 'Validee') {
                throw WorkflowException::prerequisiteNotMet(
                    $codeEtat,
                    'La candidature doit être validée'
                );
            }
        }

        // Gate: Paiement vérifié avant passage en commission
        if ($codeEtat === WorkflowEtat::ETAT_EN_ATTENTE_COMMISSION) {
            if (!$this->verifierPaiementComplet($dossier)) {
                throw WorkflowException::prerequisiteNotMet(
                    $codeEtat,
                    'Le paiement doit être complet'
                );
            }
        }

        // Gate: Rapport soumis avant commission
        if ($codeEtat === WorkflowEtat::ETAT_EN_EVALUATION_COMMISSION) {
            $rapport = $dossier->getRapport();
            if ($rapport === null || !in_array($rapport->statut, ['Soumis', 'En_evaluation'], true)) {
                throw WorkflowException::prerequisiteNotMet(
                    $codeEtat,
                    'Le rapport doit être soumis'
                );
            }
        }

        // Gate: Rapport validé par commission avant jury
        if ($codeEtat === WorkflowEtat::ETAT_PRET_POUR_JURY) {
            $rapport = $dossier->getRapport();
            if ($rapport === null || $rapport->statut !== 'Valide') {
                throw WorkflowException::prerequisiteNotMet(
                    $codeEtat,
                    'Le rapport doit être validé par la commission'
                );
            }
        }

        // Gate: Jury complet avant planification soutenance
        if ($codeEtat === WorkflowEtat::ETAT_SOUTENANCE_PLANIFIEE) {
            $jury = $dossier->getJury();
            if (count($jury) < 3) {
                throw WorkflowException::prerequisiteNotMet(
                    $codeEtat,
                    'Le jury doit être complet (minimum 3 membres)'
                );
            }
        }

        // Gate: Soutenance planifiée avant début
        if ($codeEtat === WorkflowEtat::ETAT_SOUTENANCE_EN_COURS) {
            $soutenance = $dossier->getSoutenance();
            if ($soutenance === null || $soutenance->statut !== 'Planifiee') {
                throw WorkflowException::prerequisiteNotMet(
                    $codeEtat,
                    'La soutenance doit être planifiée'
                );
            }
        }

        // Gate: Notes saisies avant terminer soutenance
        if ($codeEtat === WorkflowEtat::ETAT_SOUTENANCE_TERMINEE) {
            $soutenance = $dossier->getSoutenance();
            $soutenanceId = $soutenance->id_soutenance ?? null;
            if ($soutenance === null || $soutenanceId === null || !$this->notesSaisies((int) $soutenanceId)) {
                throw WorkflowException::prerequisiteNotMet(
                    $codeEtat,
                    'Les notes doivent être saisies'
                );
            }
        }
    }

    /**
     * Vérifie si le paiement est complet pour un dossier
     */
    private function verifierPaiementComplet(DossierEtudiant $dossier): bool
    {
        $sql = "SELECT COALESCE(SUM(montant_paye), 0) as total_paye,
                       COALESCE((SELECT montant FROM frais_scolarite WHERE annee_acad_id = :annee), 0) as total_du
                FROM paiements 
                WHERE etudiant_id = :etudiant 
                AND annee_acad_id = :annee2
                AND statut = 'Valide'";

        $stmt = Model::raw($sql, [
            'etudiant' => $dossier->etudiant_id,
            'annee' => $dossier->annee_acad_id,
            'annee2' => $dossier->annee_acad_id,
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) {
            return true; // Pas de frais configurés
        }

        return (float) $result['total_paye'] >= (float) $result['total_du'];
    }

    /**
     * Vérifie si les notes sont saisies pour une soutenance
     */
    private function notesSaisies(int $soutenanceId): bool
    {
        $sql = "SELECT COUNT(*) FROM notes_soutenances WHERE soutenance_id = :id";
        $stmt = Model::raw($sql, ['id' => $soutenanceId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Génère un snapshot JSON du dossier
     */
    public function genererSnapshot(DossierEtudiant $dossier): array
    {
        $etudiant = $dossier->getEtudiant();
        $etat = $dossier->getEtatActuel();
        $candidature = $dossier->getCandidature();
        $rapport = $dossier->getRapport();
        $soutenance = $dossier->getSoutenance();

        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'dossier' => [
                'id' => $dossier->getId(),
                'etat_actuel' => $etat?->code_etat,
                'date_entree_etat' => $dossier->date_entree_etat,
                'date_limite_etat' => $dossier->date_limite_etat,
            ],
            'etudiant' => $etudiant ? [
                'id' => $etudiant->getId(),
                'nom' => $etudiant->nom_etu ?? null,
                'prenom' => $etudiant->prenom_etu ?? null,
            ] : null,
            'candidature' => $candidature ? [
                'statut' => $candidature->statut ?? null,
            ] : null,
            'rapport' => $rapport ? [
                'version' => $rapport->version ?? null,
                'statut' => $rapport->statut ?? null,
            ] : null,
            'soutenance' => $soutenance ? [
                'date' => $soutenance->date_soutenance ?? null,
                'statut' => $soutenance->statut ?? null,
            ] : null,
        ];
    }

    /**
     * Envoie les notifications liées à une transition
     */
    private function envoyerNotificationsTransition(
        DossierEtudiant $dossier,
        WorkflowEtat $etatSource,
        WorkflowEtat $etatCible
    ): void {
        $etudiant = $dossier->getEtudiant();
        if ($etudiant === null) {
            return;
        }

        // Déterminer le template de notification
        $templateCode = 'workflow_transition_' . strtolower($etatCible->code_etat);

        ServiceNotification::envoyerParCode(
            $templateCode,
            [(int) $etudiant->utilisateur_id],
            [
                'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
                'etat_precedent' => $etatSource->nom_etat,
                'etat_nouveau' => $etatCible->nom_etat,
                'date' => date('d/m/Y H:i'),
            ]
        );
    }

    /**
     * Vérifie les SLA et génère des alertes
     */
    public function verifierSLA(): array
    {
        $alertes = [];

        // Récupérer tous les dossiers avec délai
        $sql = "SELECT * FROM dossiers_etudiants WHERE date_limite_etat IS NOT NULL";
        $stmt = Model::raw($sql);
        $dossiers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($dossiers as $row) {
            $dossier = new DossierEtudiant($row);
            $dossier->exists = true;

            $pourcentage = $dossier->pourcentageDelai();
            $etat = $dossier->getEtatActuel();

            // Déterminer le niveau d'alerte
            $niveau = null;
            if ($pourcentage >= self::SLA_SEUIL_ROUGE) {
                $niveau = 'rouge';
            } elseif ($pourcentage >= self::SLA_SEUIL_ORANGE) {
                $niveau = 'orange';
            } elseif ($pourcentage >= self::SLA_SEUIL_JAUNE) {
                $niveau = 'jaune';
            }

            if ($niveau !== null) {
                $alerte = $this->creerAlerte($dossier, $niveau, $pourcentage);
                if ($alerte !== null) {
                    $alertes[] = $alerte;
                }
            }
        }

        return $alertes;
    }

    /**
     * Crée une alerte SLA si elle n'existe pas déjà
     */
    private function creerAlerte(DossierEtudiant $dossier, string $niveau, int $pourcentage): ?array
    {
        // Vérifier si une alerte similaire existe déjà pour aujourd'hui
        $sql = "SELECT COUNT(*) FROM workflow_alertes 
                WHERE dossier_id = :dossier 
                AND niveau = :niveau 
                AND DATE(created_at) = CURDATE()";

        $stmt = Model::raw($sql, [
            'dossier' => $dossier->getId(),
            'niveau' => $niveau,
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            return null; // Alerte déjà créée aujourd'hui
        }

        // Créer l'alerte
        $etat = $dossier->getEtatActuel();
        $alerte = new WorkflowAlerte([
            'dossier_id' => $dossier->getId(),
            'etat_id' => $dossier->etat_actuel_id,
            'niveau' => $niveau,
            'pourcentage_delai' => $pourcentage,
            'message' => sprintf(
                'Délai à %d%% pour le dossier #%d (état: %s)',
                $pourcentage,
                $dossier->getId(),
                $etat?->nom_etat ?? 'inconnu'
            ),
        ]);
        $alerte->save();

        return [
            'dossier_id' => $dossier->getId(),
            'niveau' => $niveau,
            'pourcentage' => $pourcentage,
            'etat' => $etat?->code_etat,
        ];
    }

    /**
     * Retourne les transitions possibles pour un dossier
     */
    public function getTransitionsPossibles(int $dossierId): array
    {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            return [];
        }

        $etatActuel = $dossier->getEtatActuel();
        if ($etatActuel === null) {
            return [];
        }

        $transitions = WorkflowTransition::depuisEtat($etatActuel->getId());
        $result = [];

        foreach ($transitions as $transition) {
            $etatCible = $transition->etatCible();
            if ($etatCible !== null) {
                $result[] = [
                    'code' => $transition->code_transition,
                    'nom' => $transition->nom_transition,
                    'etat_cible' => $etatCible->code_etat,
                    'etat_cible_nom' => $etatCible->nom_etat,
                ];
            }
        }

        return $result;
    }

    /**
     * Retourne l'historique des transitions d'un dossier
     */
    public function getHistorique(int $dossierId): array
    {
        return WorkflowHistorique::pourDossier($dossierId);
    }

    /**
     * Retourne les statistiques du workflow
     */
    public function getStatistiques(): array
    {
        return WorkflowEtat::statistiques();
    }
}
