<?php

declare(strict_types=1);

namespace App\Controllers\Soutenance;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Services\Soutenance\ServiceJury;
use App\Models\JuryMembre;
use App\Models\DossierEtudiant;
use App\Models\Enseignant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;
use App\Orm\Model;

/**
 * Contrôleur Jury Soutenance
 * 
 * Gestion de la constitution des jurys.
 * 5 membres requis, workflow d'invitation/acceptation.
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
class JuryController
{
    private ServiceJury $serviceJury;

    public function __construct()
    {
        $this->serviceJury = new ServiceJury();
    }

    /**
     * Affiche la page de gestion des jurys
     */
    public function index(): Response
    {
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/modules/soutenance/jury/index.php';
        $content = ob_get_clean();

        return Response::html($content);
    }

    /**
     * Liste tous les jurys (API)
     */
    public function list(): Response
    {
        $dossierId = Request::get('dossier_id');
        $statut = Request::get('statut');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT jm.*, 
                       e.nom_ens, e.prenom_ens, e.email_ens, e.grade,
                       de.id_dossier, et.nom_etu, et.prenom_etu
                FROM jury_membres jm
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                INNER JOIN dossiers_etudiants de ON de.id_dossier = jm.dossier_id
                INNER JOIN etudiants et ON et.id_etudiant = de.etudiant_id
                WHERE 1=1";

        $params = [];

        if ($dossierId) {
            $sql .= " AND jm.dossier_id = :dossier";
            $params['dossier'] = $dossierId;
        }

        if ($statut) {
            $sql .= " AND jm.statut = :statut";
            $params['statut'] = $statut;
        }

        $sql .= " ORDER BY de.id_dossier, jm.role";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = Model::raw($sql, $params);
        $membres = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $membres,
        ]);
    }

    /**
     * Affiche un membre du jury
     */
    public function show(int $id): Response
    {
        $membre = JuryMembre::find($id);
        if ($membre === null) {
            return Response::json(['error' => 'Membre non trouvé'], 404);
        }

        $enseignant = Enseignant::find((int) $membre->enseignant_id);

        return Response::json([
            'success' => true,
            'data' => [
                'membre' => $membre->toArray(),
                'enseignant' => $enseignant?->toArray(),
            ],
        ]);
    }

    /**
     * Ajoute un membre au jury
     */
    public function store(): Response
    {
        $dossierId = (int) Request::post('dossier_id');
        $enseignantId = (int) Request::post('enseignant_id');
        $role = Request::post('role') ?? 'Examinateur';
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $membre = $this->serviceJury->ajouterMembre(
                $dossierId,
                $enseignantId,
                $role,
                (int) $utilisateurId
            );

            return Response::json([
                'success' => true,
                'message' => 'Membre ajouté au jury',
                'data' => ['id' => $membre->getId()],
            ], 201);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Accepte une invitation au jury
     */
    public function accepter(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $this->serviceJury->accepterInvitation($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Participation acceptée',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Refuse une invitation au jury
     */
    public function refuser(int $id): Response
    {
        $motif = Request::post('motif') ?? '';
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $this->serviceJury->refuserInvitation($id, $motif, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Participation refusée',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retire un membre du jury
     */
    public function retirer(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        try {
            $this->serviceJury->retirerMembre($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Membre retiré du jury',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retourne les membres d'un jury pour un dossier
     */
    public function parDossier(int $dossierId): Response
    {
        $membres = $this->serviceJury->getMembres($dossierId);
        $estComplet = $this->serviceJury->estComplet($dossierId);
        $nombreMembres = $this->serviceJury->compterMembres($dossierId);
        $nombreAcceptes = $this->serviceJury->compterMembresAcceptes($dossierId);

        return Response::json([
            'success' => true,
            'data' => [
                'membres' => $membres,
                'est_complet' => $estComplet,
                'nombre_total' => $nombreMembres,
                'nombre_acceptes' => $nombreAcceptes,
            ],
        ]);
    }

    /**
     * Recherche des enseignants disponibles pour le jury
     */
    public function enseignantsDisponibles(): Response
    {
        $specialite = Request::get('specialite');
        $recherche = Request::get('q');
        $dossierId = Request::get('dossier_id');

        $sql = "SELECT e.id_enseignant, e.nom_ens, e.prenom_ens, e.email_ens, 
                       e.grade, s.libelle_specialite
                FROM enseignants e
                LEFT JOIN specialites s ON s.id_specialite = e.specialite_id
                WHERE e.actif = 1";

        $params = [];

        if ($specialite) {
            $sql .= " AND e.specialite_id = :specialite";
            $params['specialite'] = $specialite;
        }

        if ($recherche) {
            $sql .= " AND (e.nom_ens LIKE :q1 OR e.prenom_ens LIKE :q2)";
            $params['q1'] = "%{$recherche}%";
            $params['q2'] = "%{$recherche}%";
        }

        // Exclure ceux déjà dans le jury
        if ($dossierId) {
            $sql .= " AND e.id_enseignant NOT IN (
                        SELECT enseignant_id FROM jury_membres WHERE dossier_id = :dossier
                      )";
            $params['dossier'] = $dossierId;
        }

        $sql .= " ORDER BY e.nom_ens, e.prenom_ens LIMIT 50";

        $stmt = Model::raw($sql, $params);
        $enseignants = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $enseignants,
        ]);
    }

    /**
     * Retourne les invitations en attente pour un enseignant
     */
    public function mesInvitations(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $sql = "SELECT jm.*, de.id_dossier, et.nom_etu, et.prenom_etu,
                       c.theme as theme_memoire, s.date_soutenance
                FROM jury_membres jm
                INNER JOIN dossiers_etudiants de ON de.id_dossier = jm.dossier_id
                INNER JOIN etudiants et ON et.id_etudiant = de.etudiant_id
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                LEFT JOIN candidatures c ON c.dossier_id = de.id_dossier
                LEFT JOIN soutenances s ON s.dossier_id = de.id_dossier
                WHERE e.utilisateur_id = :user
                AND jm.statut = 'Invite'
                ORDER BY s.date_soutenance ASC";

        $stmt = Model::raw($sql, ['user' => $utilisateurId]);
        $invitations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $invitations,
        ]);
    }

    /**
     * Statistiques des jurys
     */
    public function statistiques(): Response
    {
        $sql = "SELECT 
                    COUNT(DISTINCT dossier_id) as total_dossiers,
                    COUNT(*) as total_membres,
                    SUM(CASE WHEN statut = 'Accepte' THEN 1 ELSE 0 END) as acceptes,
                    SUM(CASE WHEN statut = 'Refuse' THEN 1 ELSE 0 END) as refuses,
                    SUM(CASE WHEN statut = 'Invite' THEN 1 ELSE 0 END) as en_attente
                FROM jury_membres";

        $stmt = Model::raw($sql);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
