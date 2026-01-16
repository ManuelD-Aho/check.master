<?php

declare(strict_types=1);

namespace App\Controllers\Communication;

use App\Services\Communication\ServiceNotification;
use App\Models\NotificationTemplate;
use App\Models\NotificationHistorique;
use Src\Http\Response;
use Src\Http\Request;
use App\Orm\Model;

/**
 * Contrôleur des notifications
 * 
 * Gestion des notifications et templates.
 * 
 * @see PRD 05 - Communication
 */
class NotificationsController
{
    /**
     * Liste les notifications en file d'attente
     */
    public function list(): Response
    {
        $limite = (int) (Request::get('limite') ?? 100);
        $notifications = ServiceNotification::getEnAttente($limite);

        return Response::json([
            'success' => true,
            'data' => array_map(fn($n) => $n->toArray(), $notifications),
        ]);
    }

    /**
     * Retourne l'historique des notifications
     */
    public function historique(): Response
    {
        $utilisateurId = Request::get('utilisateur_id');
        $canal = Request::get('canal');
        $page = (int) (Request::get('page') ?? 1);
        $perPage = (int) (Request::get('per_page') ?? 50);

        $sql = "SELECT nh.*, nt.code_template, nt.sujet_template
                FROM notifications_historique nh
                LEFT JOIN notification_templates nt ON nt.id_template = nh.template_id
                WHERE 1=1";

        $params = [];

        if ($utilisateurId) {
            $sql .= " AND nh.destinataire_id = :user";
            $params['user'] = $utilisateurId;
        }

        if ($canal) {
            $sql .= " AND nh.canal = :canal";
            $params['canal'] = $canal;
        }

        $sql .= " ORDER BY nh.created_at DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = Model::raw($sql, $params);
        $historique = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $historique,
        ]);
    }

    /**
     * Retourne les templates de notification
     */
    public function templates(): Response
    {
        $canal = Request::get('canal');
        $actif = Request::get('actif');

        $sql = "SELECT * FROM notification_templates WHERE 1=1";
        $params = [];

        if ($canal) {
            $sql .= " AND canal = :canal";
            $params['canal'] = $canal;
        }

        if ($actif !== null) {
            $sql .= " AND actif = :actif";
            $params['actif'] = $actif === '1' ? 1 : 0;
        }

        $sql .= " ORDER BY code_template";

        $stmt = Model::raw($sql, $params);
        $templates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Envoie une notification
     */
    public function envoyer(): Response
    {
        $codeTemplate = Request::post('code_template');
        $destinataires = Request::post('destinataires');
        $variables = Request::post('variables');

        if (empty($codeTemplate) || empty($destinataires)) {
            return Response::json(['error' => 'Le code template et les destinataires sont requis'], 422);
        }

        if (is_string($destinataires)) {
            $destinataires = array_map('intval', explode(',', $destinataires));
        }

        if (is_string($variables)) {
            $variables = json_decode($variables, true) ?? [];
        }

        $count = ServiceNotification::envoyerParCode(
            $codeTemplate,
            $destinataires,
            $variables
        );

        return Response::json([
            'success' => true,
            'message' => "{$count} notification(s) ajoutée(s) à la file",
            'data' => ['count' => $count],
        ]);
    }

    /**
     * Traite la file d'attente
     */
    public function traiterFile(): Response
    {
        $limite = (int) (Request::post('limite') ?? 50);
        $resultats = ServiceNotification::traiterFileAttente($limite);

        return Response::json([
            'success' => true,
            'message' => 'Traitement terminé',
            'data' => $resultats,
        ]);
    }

    /**
     * Retourne les statistiques
     */
    public function statistiques(): Response
    {
        $stats = ServiceNotification::getStatistiques();

        return Response::json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
