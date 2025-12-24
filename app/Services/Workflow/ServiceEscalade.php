<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Models\Escalade;
use App\Models\EscaladeAction;
use App\Models\EscaladeNiveau;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\EscaladeException;
use Src\Exceptions\NotFoundException;

/**
 * Service Escalade
 * 
 * Gère les escalades suite à des blocages workflow ou dépassements de délai.
 * Implémente l'escalade par niveau jusqu'au Doyen.
 * 
 * @see PRD Section Escalade
 */
class ServiceEscalade
{
    /**
     * Crée une nouvelle escalade
     */
    public function creerEscalade(
        int $dossierId,
        string $type,
        string $description,
        int $creePar,
        ?int $assigneeA = null
    ): Escalade {
        // Déterminer l'assigné par défaut selon le type
        if ($assigneeA === null) {
            $assigneeA = $this->determinerAssigneParDefaut($type);
        }

        $escalade = Escalade::creer(
            $dossierId,
            $type,
            $description,
            $creePar,
            $assigneeA,
            1 // Niveau initial
        );

        ServiceAudit::logCreation('escalade', $escalade->getId(), [
            'dossier_id' => $dossierId,
            'type' => $type,
            'description' => $description,
        ]);

        // Notifier l'assigné
        if ($assigneeA !== null) {
            $this->notifierNouvelleEscalade($escalade, $assigneeA);
        }

        return $escalade;
    }

    /**
     * Détermine l'assigné par défaut selon le type d'escalade
     */
    private function determinerAssigneParDefaut(string $type): ?int
    {
        // Récupérer depuis la configuration des niveaux
        $niveau = EscaladeNiveau::getNiveauParType($type, 1);
        return $niveau?->utilisateur_defaut_id;
    }

    /**
     * Prend en charge une escalade
     */
    public function prendreEnCharge(int $escaladeId, int $utilisateurId): bool
    {
        $escalade = Escalade::find($escaladeId);
        if ($escalade === null) {
            throw new NotFoundException('Escalade non trouvée');
        }

        if (!$escalade->estActive()) {
            throw new EscaladeException('Cette escalade n\'est plus active');
        }

        $escalade->prendreEnCharge($utilisateurId);

        ServiceAudit::log('prise_en_charge_escalade', 'escalade', $escaladeId, [
            'utilisateur_id' => $utilisateurId,
        ]);

        return true;
    }

    /**
     * Ajoute une action à l'escalade
     */
    public function ajouterAction(
        int $escaladeId,
        int $utilisateurId,
        string $typeAction,
        string $description
    ): EscaladeAction {
        $escalade = Escalade::find($escaladeId);
        if ($escalade === null) {
            throw new NotFoundException('Escalade non trouvée');
        }

        $action = EscaladeAction::enregistrer(
            $escaladeId,
            $utilisateurId,
            $typeAction,
            $description
        );

        ServiceAudit::log('action_escalade', 'escalade', $escaladeId, [
            'type_action' => $typeAction,
            'description' => $description,
        ]);

        return $action;
    }

    /**
     * Résoud une escalade
     */
    public function resoudre(
        int $escaladeId,
        int $utilisateurId,
        string $resolution
    ): bool {
        $escalade = Escalade::find($escaladeId);
        if ($escalade === null) {
            throw new NotFoundException('Escalade non trouvée');
        }

        if (!$escalade->estActive()) {
            throw new EscaladeException('Cette escalade n\'est plus active');
        }

        $escalade->resoudre($utilisateurId, $resolution);

        ServiceAudit::log('resolution_escalade', 'escalade', $escaladeId, [
            'resolution' => $resolution,
        ]);

        // Notifier le créateur de l'escalade
        $this->notifierResolution($escalade);

        return true;
    }

    /**
     * Escalade au niveau supérieur
     */
    public function escaladerNiveauSuperieur(
        int $escaladeId,
        int $utilisateurId,
        string $motif
    ): bool {
        $escalade = Escalade::find($escaladeId);
        if ($escalade === null) {
            throw new NotFoundException('Escalade non trouvée');
        }

        if (!$escalade->estActive()) {
            throw new EscaladeException('Cette escalade n\'est plus active');
        }

        // Déterminer le nouvel assigné au niveau supérieur
        $niveauSuivant = ((int) $escalade->niveau_escalade) + 1;
        $niveau = EscaladeNiveau::getNiveauParType((string) $escalade->type_escalade, $niveauSuivant);
        $nouvelAssigne = $niveau?->utilisateur_defaut_id;

        $escalade->escaladerNiveauSuperieur($utilisateurId, $nouvelAssigne);

        // Ajouter l'action d'escalade
        EscaladeAction::enregistrer(
            $escaladeId,
            $utilisateurId,
            'escalade_superieure',
            "Escalade au niveau {$niveauSuivant}: {$motif}"
        );

        ServiceAudit::log('escalade_niveau_superieur', 'escalade', $escaladeId, [
            'niveau' => $niveauSuivant,
            'motif' => $motif,
        ]);

        // Notifier le nouvel assigné
        if ($nouvelAssigne !== null) {
            $this->notifierNouvelleEscalade($escalade, $nouvelAssigne);
        }

        return true;
    }

    /**
     * Ferme une escalade sans résolution
     */
    public function fermer(
        int $escaladeId,
        int $utilisateurId,
        string $motif
    ): bool {
        $escalade = Escalade::find($escaladeId);
        if ($escalade === null) {
            throw new NotFoundException('Escalade non trouvée');
        }

        $escalade->fermer($utilisateurId, $motif);

        ServiceAudit::log('fermeture_escalade', 'escalade', $escaladeId, [
            'motif' => $motif,
        ]);

        return true;
    }

    /**
     * Notifie un nouvel assigné
     */
    private function notifierNouvelleEscalade(Escalade $escalade, int $utilisateurId): void
    {
        ServiceNotification::envoyerParCode(
            'escalade_nouvelle',
            [$utilisateurId],
            [
                'escalade_id' => $escalade->getId(),
                'type' => $escalade->type_escalade,
                'description' => $escalade->description,
                'niveau' => $escalade->niveau_escalade,
            ]
        );
    }

    /**
     * Notifie la résolution d'une escalade
     */
    private function notifierResolution(Escalade $escalade): void
    {
        $createur = $escalade->createur();
        if ($createur === null) {
            return;
        }

        ServiceNotification::envoyerParCode(
            'escalade_resolue',
            [$createur->getId()],
            [
                'escalade_id' => $escalade->getId(),
                'type' => $escalade->type_escalade,
            ]
        );
    }

    /**
     * Retourne les escalades ouvertes
     */
    public function getEscaladesOuvertes(): array
    {
        return Escalade::ouvertes();
    }

    /**
     * Retourne les escalades assignées à un utilisateur
     */
    public function getEscaladesAssigneesA(int $utilisateurId): array
    {
        return Escalade::assigneesA($utilisateurId);
    }

    /**
     * Retourne les escalades d'un dossier
     */
    public function getEscaladesDossier(int $dossierId): array
    {
        return Escalade::pourDossier($dossierId);
    }

    /**
     * Retourne les statistiques d'escalade
     */
    public function getStatistiques(): array
    {
        return [
            'actives' => Escalade::nombreActives(),
            'par_type' => Escalade::statistiquesParType(),
        ];
    }
}
