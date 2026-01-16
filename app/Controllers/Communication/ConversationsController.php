<?php

declare(strict_types=1);

namespace App\Controllers\Communication;

use App\Services\Communication\ServiceConversation;
use Src\Http\Response;
use Src\Http\Request;

/**
 * Contrôleur des conversations
 * 
 * Gestion des conversations liées aux dossiers.
 * 
 * @see PRD 05 - Communication
 */
class ConversationsController
{
    /**
     * Retourne les conversations de l'utilisateur
     */
    public function mesConversations(): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $conversations = ServiceConversation::getConversationsUtilisateur((int) $utilisateurId);

        return Response::json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Retourne une conversation de dossier
     */
    public function parDossier(int $dossierId): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        try {
            $messages = ServiceConversation::obtenirConversationDossier($dossierId);
            $participants = ServiceConversation::getParticipantsDossier($dossierId);
            $nonLus = ServiceConversation::compterNonLus($dossierId, (int) $utilisateurId);

            return Response::json([
                'success' => true,
                'data' => [
                    'messages' => $messages,
                    'participants' => $participants,
                    'non_lus' => $nonLus,
                ],
            ]);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Envoie un message dans une conversation de dossier
     */
    public function envoyerMessage(int $dossierId): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $contenu = Request::post('contenu') ?? '';
        $pieceJointe = Request::post('piece_jointe');

        if (empty($contenu)) {
            return Response::json(['error' => 'Le contenu est requis'], 422);
        }

        try {
            $message = ServiceConversation::envoyerDansDossier(
                $dossierId,
                (int) $utilisateurId,
                $contenu,
                $pieceJointe
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
     * Marque une conversation comme lue
     */
    public function marquerLue(int $dossierId): Response
    {
        $utilisateurId = Request::session('utilisateur_id');
        if (!$utilisateurId) {
            return Response::json(['error' => 'Non authentifié'], 401);
        }

        $count = ServiceConversation::marquerConversationLue($dossierId, (int) $utilisateurId);

        return Response::json([
            'success' => true,
            'message' => 'Conversation marquée comme lue',
            'data' => ['messages_marques' => $count],
        ]);
    }
}
