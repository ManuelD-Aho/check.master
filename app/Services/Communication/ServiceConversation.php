<?php

declare(strict_types=1);

namespace App\Services\Communication;

use App\Models\MessageInterne;
use App\Models\DossierEtudiant;
use App\Models\Utilisateur;
use App\Services\Security\ServiceAudit;
use Src\Exceptions\NotFoundException;
use App\Orm\Model;

/**
 * Service Conversation
 * 
 * Gestion des conversations contextuelles liées aux dossiers.
 * Participants automatiques : étudiant, directeur, encadreur.
 * 
 * @see PRD 05 - Communication
 */
class ServiceConversation
{
    /**
     * Types de conversation
     */
    public const TYPE_DOSSIER = 'Dossier';
    public const TYPE_DIRECT = 'Direct';
    public const TYPE_GROUPE = 'Groupe';
    public const TYPE_SYSTEME = 'Systeme';

    /**
     * Crée ou récupère une conversation pour un dossier
     */
    public static function obtenirConversationDossier(int $dossierId): array
    {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            throw new NotFoundException('Dossier non trouvé');
        }

        // Récupérer tous les messages de la conversation du dossier
        $sql = "SELECT mi.*, 
                       u.login_utilisateur as expediteur_email,
                       COALESCE(e.nom_etu, ens.nom_ens, pa.nom_pa, 'Système') as expediteur_nom,
                       COALESCE(e.prenom_etu, ens.prenom_ens, pa.prenom_pa, '') as expediteur_prenom
                FROM messages_internes mi
                LEFT JOIN utilisateurs u ON u.id_utilisateur = mi.expediteur_id
                LEFT JOIN etudiants e ON e.utilisateur_id = mi.expediteur_id
                LEFT JOIN enseignants ens ON ens.utilisateur_id = mi.expediteur_id
                LEFT JOIN personnel_admin pa ON pa.utilisateur_id = mi.expediteur_id
                WHERE mi.contexte_type = 'dossier' AND mi.contexte_id = :dossier
                ORDER BY mi.created_at ASC";

        $stmt = Model::raw($sql, ['dossier' => $dossierId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Envoie un message dans une conversation de dossier
     */
    public static function envoyerDansDossier(
        int $dossierId,
        int $expediteurId,
        string $contenu,
        ?string $pieceJointe = null
    ): MessageInterne {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            throw new NotFoundException('Dossier non trouvé');
        }

        // Vérifier que l'expéditeur est autorisé (participant au dossier)
        $participants = self::getParticipantsDossier($dossierId);
        $participantIds = array_column($participants, 'utilisateur_id');

        if (!in_array($expediteurId, $participantIds, true)) {
            throw new NotFoundException('Non autorisé à participer à cette conversation');
        }

        // Créer le message
        $message = new MessageInterne([
            'expediteur_id' => $expediteurId,
            'destinataire_id' => null, // Conversation de groupe
            'sujet' => 'Conversation Dossier #' . $dossierId,
            'contenu' => $contenu,
            'contexte_type' => 'dossier',
            'contexte_id' => $dossierId,
            'piece_jointe' => $pieceJointe,
            'lu' => false,
        ]);
        $message->save();

        ServiceAudit::log('message_conversation', 'message', $message->getId(), [
            'dossier_id' => $dossierId,
        ]);

        // Notifier les autres participants
        self::notifierParticipants($dossierId, $expediteurId, $contenu);

        return $message;
    }

    /**
     * Retourne les participants d'un dossier
     */
    public static function getParticipantsDossier(int $dossierId): array
    {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            return [];
        }

        $participants = [];

        // 1. L'étudiant
        $etudiant = $dossier->getEtudiant();
        if ($etudiant !== null && $etudiant->utilisateur_id !== null) {
            $participants[] = [
                'utilisateur_id' => (int) $etudiant->utilisateur_id,
                'type' => 'etudiant',
                'nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
            ];
        }

        // 2. Le directeur de mémoire
        $directeur = $dossier->getDirecteur();
        if ($directeur !== null && $directeur->utilisateur_id !== null) {
            $participants[] = [
                'utilisateur_id' => (int) $directeur->utilisateur_id,
                'type' => 'directeur',
                'nom' => $directeur->nom_ens . ' ' . $directeur->prenom_ens,
            ];
        }

        // 3. L'encadreur pédagogique
        $encadreur = $dossier->getEncadreur();
        if ($encadreur !== null && $encadreur->utilisateur_id !== null) {
            $participants[] = [
                'utilisateur_id' => (int) $encadreur->utilisateur_id,
                'type' => 'encadreur',
                'nom' => $encadreur->nom_ens . ' ' . $encadreur->prenom_ens,
            ];
        }

        return $participants;
    }

    /**
     * Notifie les participants d'un nouveau message
     */
    private static function notifierParticipants(int $dossierId, int $expediteurId, string $contenu): void
    {
        $participants = self::getParticipantsDossier($dossierId);

        foreach ($participants as $participant) {
            if ($participant['utilisateur_id'] === $expediteurId) {
                continue; // Ne pas notifier l'expéditeur
            }

            ServiceNotification::envoyerParCode(
                'nouveau_message_dossier',
                [$participant['utilisateur_id']],
                [
                    'dossier_id' => $dossierId,
                    'extrait' => mb_substr($contenu, 0, 100) . '...',
                ]
            );
        }
    }

    /**
     * Marque les messages d'une conversation comme lus pour un utilisateur
     */
    public static function marquerConversationLue(int $dossierId, int $utilisateurId): int
    {
        $sql = "UPDATE messages_internes 
                SET lu = 1, lu_le = NOW()
                WHERE contexte_type = 'dossier' 
                AND contexte_id = :dossier
                AND expediteur_id != :utilisateur
                AND (lu = 0 OR lu IS NULL)";

        $stmt = Model::raw($sql, [
            'dossier' => $dossierId,
            'utilisateur' => $utilisateurId,
        ]);

        return $stmt->rowCount();
    }

    /**
     * Compte les messages non lus d'un dossier pour un utilisateur
     */
    public static function compterNonLus(int $dossierId, int $utilisateurId): int
    {
        $sql = "SELECT COUNT(*) FROM messages_internes 
                WHERE contexte_type = 'dossier' 
                AND contexte_id = :dossier
                AND expediteur_id != :utilisateur
                AND (lu = 0 OR lu IS NULL)";

        $stmt = Model::raw($sql, [
            'dossier' => $dossierId,
            'utilisateur' => $utilisateurId,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne toutes les conversations d'un utilisateur
     */
    public static function getConversationsUtilisateur(int $utilisateurId): array
    {
        // Récupérer les dossiers où l'utilisateur est participant
        $sql = "SELECT DISTINCT de.id_dossier, de.etudiant_id, 
                       e.nom_etu, e.prenom_etu,
                       (SELECT MAX(mi.created_at) FROM messages_internes mi 
                        WHERE mi.contexte_type = 'dossier' AND mi.contexte_id = de.id_dossier) as dernier_message,
                       (SELECT COUNT(*) FROM messages_internes mi 
                        WHERE mi.contexte_type = 'dossier' AND mi.contexte_id = de.id_dossier
                        AND mi.expediteur_id != :uid AND (mi.lu = 0 OR mi.lu IS NULL)) as non_lus
                FROM dossiers_etudiants de
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN enseignants dir ON dir.id_enseignant = de.directeur_id
                LEFT JOIN enseignants enc ON enc.id_enseignant = de.encadreur_id
                WHERE e.utilisateur_id = :uid1
                   OR dir.utilisateur_id = :uid2
                   OR enc.utilisateur_id = :uid3
                ORDER BY dernier_message DESC";

        $stmt = Model::raw($sql, [
            'uid' => $utilisateurId,
            'uid1' => $utilisateurId,
            'uid2' => $utilisateurId,
            'uid3' => $utilisateurId,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Supprime un message d'une conversation (soft delete)
     */
    public static function supprimerMessage(int $messageId, int $utilisateurId): bool
    {
        $message = MessageInterne::find($messageId);
        if ($message === null) {
            throw new NotFoundException('Message non trouvé');
        }

        // Seul l'expéditeur peut supprimer
        if ((int) $message->expediteur_id !== $utilisateurId) {
            return false;
        }

        $message->supprime = true;
        $message->supprime_le = date('Y-m-d H:i:s');
        $message->save();

        ServiceAudit::log('suppression_message_conversation', 'message', $messageId);

        return true;
    }

    /**
     * Ajoute une pièce jointe à un message
     */
    public static function ajouterPieceJointe(int $messageId, string $cheminFichier): bool
    {
        $message = MessageInterne::find($messageId);
        if ($message === null) {
            throw new NotFoundException('Message non trouvé');
        }

        $message->piece_jointe = $cheminFichier;
        return $message->save();
    }

    /**
     * Recherche dans les conversations d'un dossier
     */
    public static function rechercher(int $dossierId, string $terme): array
    {
        $sql = "SELECT mi.*, u.login_utilisateur as expediteur_email
                FROM messages_internes mi
                LEFT JOIN utilisateurs u ON u.id_utilisateur = mi.expediteur_id
                WHERE mi.contexte_type = 'dossier' 
                AND mi.contexte_id = :dossier
                AND mi.contenu LIKE :terme
                ORDER BY mi.created_at DESC";

        $stmt = Model::raw($sql, [
            'dossier' => $dossierId,
            'terme' => "%{$terme}%",
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
