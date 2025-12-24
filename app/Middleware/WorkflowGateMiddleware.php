<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Support\Auth;
use App\Models\DossierEtudiant;
use App\Models\WorkflowEtat;

/**
 * Middleware Gate Workflow
 * 
 * Contrôle l'accès aux onglets selon l'état du workflow.
 * Règle critique: L'onglet "Rédaction du rapport" n'est visible
 * que lorsque l'état est "candidature_validée".
 */
class WorkflowGateMiddleware
{
    private array $statesRequired;
    private string $tabName;

    public function __construct(string $tabName, array $statesRequired)
    {
        $this->tabName = $tabName;
        $this->statesRequired = $statesRequired;
    }

    /**
     * Traite la requête
     */
    public function handle(callable $next): mixed
    {
        // Vérifier l'authentification
        if (!Auth::check()) {
            return $this->redirect('/');
        }

        // Récupérer le dossier de l'utilisateur courant
        $dossier = $this->getDossierCourant();

        if ($dossier === null) {
            return $this->forbidden("Aucun dossier actif trouvé");
        }

        // Récupérer l'état actuel
        $etatActuel = $dossier->getEtatActuel();

        if ($etatActuel === null) {
            return $this->forbidden("État du dossier non défini");
        }

        // Vérifier si l'état permet l'accès
        if (!in_array($etatActuel->code_etat, $this->statesRequired, true)) {
            return $this->forbidden(
                "L'onglet '{$this->tabName}' n'est pas accessible dans l'état actuel ({$etatActuel->nom_etat})"
            );
        }

        return $next();
    }

    /**
     * Récupère le dossier courant (étudiant connecté)
     */
    private function getDossierCourant(): ?DossierEtudiant
    {
        // TODO: Adapter selon le type d'utilisateur
        // Pour un étudiant, récupérer son dossier
        // Pour un admin, récupérer le dossier en cours de traitement

        $utilisateur = Auth::user();

        if (!$utilisateur) {
            return null;
        }

        // Supposons que l'ID du dossier est en session ou en paramètre
        $dossierId = $_SESSION['dossier_id'] ?? $_GET['dossier'] ?? null;

        if ($dossierId) {
            return DossierEtudiant::find((int) $dossierId);
        }

        return null;
    }

    /**
     * Retourne une réponse 403
     */
    private function forbidden(string $message): mixed
    {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'code' => 'WORKFLOW_GATE',
            'message' => $message,
        ]);
        exit;
    }

    /**
     * Redirige
     */
    private function redirect(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Gate pour l'onglet Rédaction
     */
    public static function redaction(): self
    {
        return new self('Rédaction du rapport', [
            WorkflowEtat::ETAT_RAPPORT_VALIDE,
            WorkflowEtat::ETAT_ATTENTE_AVIS_ENCADREUR,
            WorkflowEtat::ETAT_PRET_POUR_JURY,
            WorkflowEtat::ETAT_JURY_EN_CONSTITUTION,
        ]);
    }

    /**
     * Gate pour l'onglet Soutenance
     */
    public static function soutenance(): self
    {
        return new self('Soutenance', [
            WorkflowEtat::ETAT_SOUTENANCE_PLANIFIEE,
            WorkflowEtat::ETAT_SOUTENANCE_EN_COURS,
            WorkflowEtat::ETAT_SOUTENANCE_TERMINEE,
        ]);
    }

    /**
     * Gate pour l'onglet Commission
     */
    public static function commission(): self
    {
        return new self('Commission', [
            WorkflowEtat::ETAT_EN_ATTENTE_COMMISSION,
            WorkflowEtat::ETAT_EN_EVALUATION_COMMISSION,
        ]);
    }
}
