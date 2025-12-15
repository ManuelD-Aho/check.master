<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Support\Auth;
use App\Models\Permission;
use App\Models\GroupeUtilisateur;

/**
 * Middleware de vérification des permissions
 * 
 * Vérifie que l'utilisateur a les droits sur la ressource demandée.
 */
class PermissionMiddleware
{
    private string $ressource;
    private string $action;

    public function __construct(string $ressource, string $action = 'lire')
    {
        $this->ressource = $ressource;
        $this->action = $action;
    }

    /**
     * Traite la requête
     */
    public function handle(callable $next): mixed
    {
        // Vérifier l'authentification
        if (!Auth::check()) {
            return $this->forbidden('Authentification requise');
        }

        $utilisateur = Auth::user();

        // Super admin bypass
        if ($this->estSuperAdmin($utilisateur->id_utilisateur)) {
            return $next();
        }

        // Vérifier les permissions
        if (!$this->aPermission($utilisateur->id_utilisateur, $this->ressource, $this->action)) {
            return $this->forbidden("Vous n'avez pas la permission de {$this->action} cette ressource");
        }

        return $next();
    }

    /**
     * Vérifie si l'utilisateur a la permission
     */
    private function aPermission(int $utilisateurId, string $ressource, string $action): bool
    {
        // Récupérer les groupes de l'utilisateur
        $groupes = GroupeUtilisateur::groupesUtilisateur($utilisateurId);

        foreach ($groupes as $groupe) {
            if (Permission::verifier($groupe->getId(), $ressource, $action)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur est super admin
     */
    private function estSuperAdmin(int $utilisateurId): bool
    {
        $groupes = GroupeUtilisateur::groupesUtilisateur($utilisateurId);

        foreach ($groupes as $groupe) {
            if ($groupe->nom_groupe === 'Super Admin' || $groupe->niveau_hierarchique === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne une réponse 403
     */
    private function forbidden(string $message): mixed
    {
        http_response_code(403);

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $message]);
            exit;
        }

        // Rediriger vers page d'erreur
        header('Location: /erreur/403');
        exit;
    }

    /**
     * Vérifie si c'est une requête AJAX
     */
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Factory pour créer le middleware
     */
    public static function require(string $ressource, string $action = 'lire'): self
    {
        return new self($ressource, $action);
    }
}
