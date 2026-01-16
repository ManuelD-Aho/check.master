<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Admin\ServiceAdministration;
use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Reclamation;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Réclamations Admin
 * 
 * Gestion des réclamations étudiantes pour l'administration.
 * 
 * @see PRD 08 - Administration
 */
class ReclamationsController
{
    private ServiceAdministration $service;

    public function __construct()
    {
        $this->service = new ServiceAdministration();
    }

    /**
     * Vue liste des réclamations
     */
    public function index(): Response
    {
        if (!$this->checkAccess('reclamations', 'lire')) {
            return Response::redirect('/dashboard');
        }

        $page = max(1, (int) Request::query('page', 1));
        $statut = Request::query('statut') ?: null;
        $type = Request::query('type') ?: null;

        $data = $this->service->rechercherReclamations($statut, $type, null, $page, 20);
        $stats = $this->service->statistiquesReclamations();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/reclamations.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * API: Liste des réclamations
     */
    public function list(): JsonResponse
    {
        if (!$this->checkAccess('reclamations', 'lire')) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $statut = Request::query('statut') ?: null;
        $type = Request::query('type') ?: null;
        $etudiantId = Request::query('etudiant') ? (int) Request::query('etudiant') : null;

        $data = $this->service->rechercherReclamations($statut, $type, $etudiantId, $page, 20);
        return JsonResponse::success($data);
    }

    /**
     * API: Détails d'une réclamation
     */
    public function show(int $id): JsonResponse
    {
        if (!$this->checkAccess('reclamations', 'lire')) {
            return JsonResponse::forbidden();
        }

        $reclamation = Reclamation::find($id);
        if ($reclamation === null) {
            return JsonResponse::notFound('Réclamation non trouvée');
        }

        $data = $reclamation->toArray();
        $data['etudiant'] = $reclamation->etudiant()?->toArray();
        $data['prise_en_charge_par_utilisateur'] = $reclamation->priseEnChargePar()?->toArray();
        $data['resolue_par_utilisateur'] = $reclamation->resoluePar()?->toArray();

        return JsonResponse::success($data);
    }

    /**
     * API: Prendre en charge une réclamation
     */
    public function prendreEnCharge(int $id): JsonResponse
    {
        if (!$this->checkAccess('reclamations', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $reclamation = $this->service->prendreEnChargeReclamation($id, Auth::id() ?? 0);
            return JsonResponse::success($reclamation->toArray(), 'Réclamation prise en charge');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Résoudre une réclamation
     */
    public function resoudre(int $id): JsonResponse
    {
        if (!$this->checkAccess('reclamations', 'modifier')) {
            return JsonResponse::forbidden();
        }

        $resolution = Request::input('resolution', '');
        if (empty($resolution)) {
            return JsonResponse::error('Résolution requise');
        }

        try {
            $reclamation = $this->service->resoudreReclamation($id, $resolution, Auth::id() ?? 0);
            return JsonResponse::success($reclamation->toArray(), 'Réclamation résolue');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Rejeter une réclamation
     */
    public function rejeter(int $id): JsonResponse
    {
        if (!$this->checkAccess('reclamations', 'modifier')) {
            return JsonResponse::forbidden();
        }

        $motif = Request::input('motif', '');
        if (empty($motif)) {
            return JsonResponse::error('Motif de rejet requis');
        }

        try {
            $reclamation = $this->service->rejeterReclamation($id, $motif, Auth::id() ?? 0);
            return JsonResponse::success($reclamation->toArray(), 'Réclamation rejetée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Statistiques des réclamations
     */
    public function statistiques(): JsonResponse
    {
        if (!$this->checkAccess('reclamations', 'lire')) {
            return JsonResponse::forbidden();
        }

        $stats = $this->service->statistiquesReclamations();
        return JsonResponse::success($stats);
    }

    /**
     * Vérifie les permissions
     */
    private function checkAccess(string $ressource, string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, $ressource, $action);
    }
}
