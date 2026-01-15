<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Services\Scolarite\ServiceScolarite;
use App\Services\Security\ServiceAudit;
use App\Models\Etudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Étudiants Scolarité
 */
class EtudiantsController
{
    private ServiceScolarite $service;
    private const PER_PAGE = 20;

    public function __construct()
    {
        $this->service = new ServiceScolarite();
    }

    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        
        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        
        $data = $this->getEtudiantsPagines($page, $search);
        
        // Variables pour la vue
        $etudiants = $data['etudiants'];
        $pagination = $data['pagination'];
        $searchTerm = $search;
        
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/etudiants.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * Colonnes requises pour la liste des étudiants
     */
    private const ETUDIANT_COLUMNS = 'id_etudiant, num_etu, nom_etu, prenom_etu, email_etu, promotion_etu, actif';

    /**
     * Récupère les étudiants avec pagination
     */
    private function getEtudiantsPagines(int $page, string $search = ''): array
    {
        $offset = ($page - 1) * self::PER_PAGE;
        $perPage = self::PER_PAGE;
        
        if (!empty($search)) {
            $whereClause = $this->buildSearchWhereClause();
            $searchTerm = "%{$search}%";
            
            $countSql = "SELECT COUNT(*) FROM etudiants WHERE actif = 1 AND ({$whereClause})";
            $stmt = Etudiant::raw($countSql, ['terme' => $searchTerm]);
            $total = (int) $stmt->fetchColumn();
            
            $sql = "SELECT " . self::ETUDIANT_COLUMNS . " FROM etudiants 
                    WHERE actif = 1 AND ({$whereClause})
                    ORDER BY nom_etu, prenom_etu LIMIT :limit OFFSET :offset";
            
            $pdo = Etudiant::raw("SELECT 1", [])->getConnection ?? null;
            $stmt = Etudiant::raw($sql, ['terme' => $searchTerm, 'limit' => $perPage, 'offset' => $offset]);
        } else {
            $stmt = Etudiant::raw("SELECT COUNT(*) FROM etudiants WHERE actif = 1", []);
            $total = (int) $stmt->fetchColumn();
            
            $sql = "SELECT " . self::ETUDIANT_COLUMNS . " FROM etudiants 
                    WHERE actif = 1 ORDER BY nom_etu, prenom_etu 
                    LIMIT :limit OFFSET :offset";
            $stmt = Etudiant::raw($sql, ['limit' => $perPage, 'offset' => $offset]);
        }
        
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $etudiants = array_map(function (array $row) {
            $model = new Etudiant($row);
            $model->exists = true;
            return $model;
        }, $rows);
        
        $totalPages = (int) ceil($total / self::PER_PAGE);
        
        return [
            'etudiants' => $etudiants,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'perPage' => self::PER_PAGE,
                'totalItems' => $total,
                'hasNext' => $page < $totalPages,
                'hasPrev' => $page > 1,
            ],
        ];
    }

    /**
     * Construit la clause WHERE pour la recherche
     */
    private function buildSearchWhereClause(): string
    {
        return "nom_etu LIKE :terme OR prenom_etu LIKE :terme OR 
                num_etu LIKE :terme OR email_etu LIKE :terme";
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $search = Request::query('q', '');
        $etudiants = $search ? $this->service->rechercherEtudiants($search) : Etudiant::all();
        return JsonResponse::success(array_map(fn($e) => $e->toArray(), $etudiants));
    }

    public function show(int $id): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }
        $etudiant = Etudiant::find($id);
        return $etudiant ? JsonResponse::success($etudiant->toArray()) : JsonResponse::notFound();
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }
        $data = Request::all();
        $etudiant = $this->service->creerEtudiant($data, Auth::id() ?? 0);
        return JsonResponse::success($etudiant->toArray(), 'Étudiant créé');
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'etudiants', $action);
    }
}
