<?php

declare(strict_types=1);

namespace App\Validators;

use App\Models\DossierEtudiant;
use App\Models\WorkflowEtat;

/**
 * Validateur Workflow
 * 
 * Valide les transitions de workflow.
 */
class WorkflowValidator
{
    private array $errors = [];

    /**
     * Valide une transition de workflow
     */
    public function validateTransition(
        DossierEtudiant $dossier,
        string $codeEtatCible,
        ?int $utilisateurId = null
    ): bool {
        $this->errors = [];

        // Récupérer l'état actuel
        $etatActuel = $dossier->getEtatActuel();

        if ($etatActuel === null) {
            $this->errors['etat'] = 'État actuel non défini pour ce dossier';
            return false;
        }

        // Vérifier que l'état cible existe
        $etatCible = WorkflowEtat::findByCode($codeEtatCible);

        if ($etatCible === null) {
            $this->errors['etat_cible'] = "L'état cible '{$codeEtatCible}' n'existe pas";
            return false;
        }

        // Vérifier que la transition est autorisée
        if (!$etatActuel->peutTransitionnerVers($codeEtatCible)) {
            $this->errors['transition'] = "Transition de '{$etatActuel->code_etat}' vers '{$codeEtatCible}' non autorisée";
            return false;
        }

        // Vérifier si l'état actuel est terminal
        if ($etatActuel->estTerminal()) {
            $this->errors['terminal'] = "L'état '{$etatActuel->code_etat}' est terminal. Aucune transition possible.";
            return false;
        }

        return true;
    }

    /**
     * Valide les prérequis pour une transition
     */
    public function validatePrerequisites(DossierEtudiant $dossier, string $codeEtatCible): bool
    {
        $this->errors = [];

        // Vérifier les prérequis selon l'état cible
        switch ($codeEtatCible) {
            case WorkflowEtat::JURY_EN_CONSTITUTION:
                // Vérifier que le rapport est validé
                $rapport = $dossier->getRapport();
                if (!$rapport || $rapport->statut !== 'Valide') {
                    $this->errors['rapport'] = 'Le rapport doit être validé avant la constitution du jury';
                    return false;
                }
                break;

            case WorkflowEtat::SOUTENANCE_PLANIFIEE:
                // Vérifier que le jury est complet
                $jury = $dossier->getJury();
                if (count($jury) < 3) {
                    $this->errors['jury'] = 'Le jury doit comporter au moins 3 membres';
                    return false;
                }
                break;

            case WorkflowEtat::DIPLOME_DELIVRE:
                // Vérifier que la soutenance est terminée
                $soutenance = $dossier->getSoutenance();
                if (!$soutenance || $soutenance->statut !== 'Terminee') {
                    $this->errors['soutenance'] = 'La soutenance doit être terminée';
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la première erreur
     */
    public function getFirstError(): ?string
    {
        return reset($this->errors) ?: null;
    }
}
