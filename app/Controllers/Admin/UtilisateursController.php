<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Services\Admin\ServiceAdministration;
use App\Models\Utilisateur;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;
use App\Orm\Model;

/**
 * Contrôleur Utilisateurs Admin
 * 
 * @see PRD 08 - Administration
 */
class UtilisateursController
{
    public function index(): Response
    {
        if (!$this->checkAccess('lire')) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/utilisateurs.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $statut = Request::query('statut') ?: null;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = '(nom_utilisateur LIKE :terme OR login_utilisateur LIKE :terme)';
            $params['terme'] = "%{$search}%";
        }

        if ($statut !== null) {
            $conditions[] = 'statut_utilisateur = :statut';
            $params['statut'] = $statut;
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $countSql = "SELECT COUNT(*) FROM utilisateurs {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT * FROM utilisateurs {$whereClause} ORDER BY nom_utilisateur LIMIT {$perPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return JsonResponse::success([
            'utilisateurs' => $rows,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $perPage),
                'perPage' => $perPage,
                'totalItems' => $total,
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }

        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return JsonResponse::notFound('Utilisateur non trouvé');
        }

        $data = $utilisateur->toArray();
        $data['groupes'] = array_map(fn($g) => $g->toArray(), $utilisateur->groupes());

        return JsonResponse::success($data);
    }

    public function store(): JsonResponse
    {
        if (!$this->checkAccess('creer')) {
            return JsonResponse::forbidden();
        }

        $data = Request::all();

        // Validation basique
        if (empty($data['nom_utilisateur']) || empty($data['login_utilisateur'])) {
            return JsonResponse::error('Nom et login requis');
        }

        // Vérifier unicité login
        $existant = Utilisateur::findByLogin($data['login_utilisateur']);
        if ($existant !== null) {
            return JsonResponse::error('Ce login existe déjà');
        }

        // Mot de passe par défaut si non fourni
        if (empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = password_hash('password123', PASSWORD_DEFAULT);
        } else {
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        }

        $data['statut_utilisateur'] = $data['statut_utilisateur'] ?? 'Actif';

        $utilisateur = new Utilisateur($data);
        $utilisateur->save();

        ServiceAudit::logCreation('utilisateur', $utilisateur->getId(), [
            'login' => $data['login_utilisateur'],
        ]);

        return JsonResponse::success($utilisateur->toArray(), 'Utilisateur créé');
    }

    public function update(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return JsonResponse::notFound('Utilisateur non trouvé');
        }

        $data = Request::all();

        // Exclure le mot de passe de la mise à jour normale
        unset($data['mot_de_passe']);

        $anciennesDonnees = $utilisateur->toArray();
        $utilisateur->fill($data);
        $utilisateur->save();

        ServiceAudit::logModification('utilisateur', $id, $anciennesDonnees, $data);

        return JsonResponse::success($utilisateur->toArray(), 'Utilisateur modifié');
    }

    public function activer(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return JsonResponse::notFound('Utilisateur non trouvé');
        }

        $utilisateur->statut_utilisateur = 'Actif';
        $utilisateur->save();

        ServiceAudit::log('activation_utilisateur', 'utilisateur', $id);

        return JsonResponse::success(null, 'Utilisateur activé');
    }

    public function desactiver(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return JsonResponse::notFound('Utilisateur non trouvé');
        }

        // Ne pas désactiver son propre compte
        if ($utilisateur->getId() === Auth::id()) {
            return JsonResponse::error('Vous ne pouvez pas désactiver votre propre compte');
        }

        $utilisateur->statut_utilisateur = 'Inactif';
        $utilisateur->save();

        ServiceAudit::log('desactivation_utilisateur', 'utilisateur', $id);

        return JsonResponse::success(null, 'Utilisateur désactivé');
    }

    public function resetPassword(int $id): JsonResponse
    {
        if (!$this->checkAccess('modifier')) {
            return JsonResponse::forbidden();
        }

        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return JsonResponse::notFound('Utilisateur non trouvé');
        }

        // Générer un nouveau mot de passe aléatoire
        $nouveauMdp = bin2hex(random_bytes(4)); // 8 caractères
        $utilisateur->mot_de_passe = password_hash($nouveauMdp, PASSWORD_DEFAULT);
        $utilisateur->doit_changer_mdp = true;
        $utilisateur->save();

        ServiceAudit::log('reset_mot_de_passe', 'utilisateur', $id);

        return JsonResponse::success([
            'mot_de_passe' => $nouveauMdp,
        ], 'Mot de passe réinitialisé');
    }

    public function statistiques(): JsonResponse
    {
        if (!$this->checkAccess('lire')) {
            return JsonResponse::forbidden();
        }

        $service = new ServiceAdministration();
        $stats = $service->statistiquesUtilisateurs();

        return JsonResponse::success($stats);
    }

    private function checkAccess(string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'utilisateurs', $action);
    }
}
