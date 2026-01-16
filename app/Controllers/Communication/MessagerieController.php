<?php

declare(strict_types=1);

namespace App\Controllers\Communication;

use App\Services\Communication\ServiceMessagerie;
use Src\Http\Response;
use Src\Http\Request;

/**
 * Contrôleur de la messagerie
 * 
 * Gestion des messages internes.
 * 
 * @see PRD 05 - Communication
 */
class MessagerieController
{
    /**
     * Retourne les messages reçus
     */
    public function recus(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $nonLusSeuls = Request::get('non_lus') === '1';
        $messages = ServiceMessagerie::getMessagesRecus((int) $utilisateurId, $nonLusSeuls);

        return Response::json([
            'success' => true,
            'data' => array_map(fn($m) => $m->toArray(), $messages),
        ]);
    }

    /**
     * Retourne les messages envoyés
     */
    public function envoyes(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $messages = ServiceMessagerie::getMessagesEnvoyes((int) $utilisateurId);

        return Response::json([
            'success' => true,
            'data' => array_map(fn($m) => $m->toArray(), $messages),
        ]);
    }

    /**
     * Affiche un message
     */
    public function show(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $message = ServiceMessagerie::getMessage($id, (int) $utilisateurId);
        if ($message === null) {
            return Response::json(['error' => 'Message non trouvé'], 404);
        }

        // Marquer comme lu si on est le destinataire
        if ((int) $message->destinataire_id === (int) $utilisateurId && !$message->lu) {
            ServiceMessagerie::marquerLu($id, (int) $utilisateurId);
        }

        return Response::json([
            'success' => true,
            'data' => $message->toArray(),
        ]);
    }

    /**
     * Envoie un nouveau message
     */
    public function store(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $destinataireId = (int) Request::post('destinataire_id');
        $sujet = Request::post('sujet') ?? '';
        $contenu = Request::post('contenu') ?? '';

        if (empty($destinataireId) || empty($contenu)) {
            return Response::json(['error' => 'Le destinataire et le contenu sont requis'], 422);
        }

        try {
            $message = ServiceMessagerie::envoyer(
                (int) $utilisateurId,
                $destinataireId,
                $sujet,
                $contenu
            );

            return Response::json([
                'success' => true,
                'message' => 'Message envoyé',
                'data' => ['id' => $message->getId()],
            ], 201);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Marque un message comme lu
     */
    public function marquerLu(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        try {
            ServiceMessagerie::marquerLu($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Message marqué comme lu',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Répond à un message
     */
    public function repondre(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $contenu = Request::post('contenu') ?? '';

        if (empty($contenu)) {
            return Response::json(['error' => 'Le contenu est requis'], 422);
        }

        try {
            $message = ServiceMessagerie::repondre($id, (int) $utilisateurId, $contenu);

            return Response::json([
                'success' => true,
                'message' => 'Réponse envoyée',
                'data' => ['id' => $message->getId()],
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Supprime un message
     */
    public function destroy(int $id): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        try {
            ServiceMessagerie::supprimer($id, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'message' => 'Message supprimé',
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Compte les messages non lus
     */
    public function compterNonLus(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $count = ServiceMessagerie::compterNonLus((int) $utilisateurId);

        return Response::json([
            'success' => true,
            'data' => ['count' => $count],
        ]);
    }
}
