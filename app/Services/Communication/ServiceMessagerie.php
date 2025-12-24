<?php

declare(strict_types=1);

namespace App\Services\Communication;

use App\Models\MessageInterne;
use App\Models\Utilisateur;
use App\Services\Security\ServiceAudit;
use Src\Exceptions\NotFoundException;

/**
 * Service Messagerie
 * 
 * Gestion de la messagerie interne entre utilisateurs.
 * Suivi des messages lus/non lus.
 * 
 * @see PRD Section Communication
 */
class ServiceMessagerie
{
    /**
     * Envoie un message entre utilisateurs
     */
    public static function envoyer(
        int $expediteurId,
        int $destinataireId,
        string $sujet,
        string $contenu
    ): MessageInterne {
        // Vérifier que les utilisateurs existent
        $expediteur = Utilisateur::find($expediteurId);
        $destinataire = Utilisateur::find($destinataireId);

        if ($expediteur === null || $destinataire === null) {
            throw new NotFoundException('Utilisateur non trouvé');
        }

        $message = new MessageInterne([
            'expediteur_id' => $expediteurId,
            'destinataire_id' => $destinataireId,
            'sujet' => $sujet,
            'contenu' => $contenu,
            'lu' => false,
        ]);
        $message->save();

        ServiceAudit::log('envoi_message', 'message', $message->getId(), [
            'expediteur_id' => $expediteurId,
            'destinataire_id' => $destinataireId,
            'sujet' => $sujet,
        ]);

        return $message;
    }

    /**
     * Envoie un message système (sans expéditeur humain)
     */
    public static function envoyerSysteme(
        int $destinataireId,
        string $sujet,
        string $contenu
    ): bool {
        $destinataire = Utilisateur::find($destinataireId);
        if ($destinataire === null) {
            return false;
        }

        $message = new MessageInterne([
            'expediteur_id' => null, // Message système
            'destinataire_id' => $destinataireId,
            'sujet' => $sujet,
            'contenu' => $contenu,
            'lu' => false,
        ]);
        $result = $message->save();

        return $result;
    }

    /**
     * Marque un message comme lu
     */
    public static function marquerLu(int $messageId, int $utilisateurId): bool
    {
        $message = MessageInterne::find($messageId);
        if ($message === null) {
            throw new NotFoundException('Message non trouvé');
        }

        // Vérifier que l'utilisateur est le destinataire
        if ((int) $message->destinataire_id !== $utilisateurId) {
            return false;
        }

        $message->lu = true;
        $message->lu_le = date('Y-m-d H:i:s');
        return $message->save();
    }

    /**
     * Marque plusieurs messages comme lus
     */
    public static function marquerPlusieursLus(array $messageIds, int $utilisateurId): int
    {
        $count = 0;
        foreach ($messageIds as $id) {
            try {
                if (self::marquerLu($id, $utilisateurId)) {
                    $count++;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return $count;
    }

    /**
     * Supprime un message
     */
    public static function supprimer(int $messageId, int $utilisateurId): bool
    {
        $message = MessageInterne::find($messageId);
        if ($message === null) {
            throw new NotFoundException('Message non trouvé');
        }

        // Vérifier que l'utilisateur est l'expéditeur ou le destinataire
        if ((int) $message->expediteur_id !== $utilisateurId && (int) $message->destinataire_id !== $utilisateurId) {
            return false;
        }

        ServiceAudit::log('suppression_message', 'message', $messageId);

        return $message->delete();
    }

    /**
     * Retourne les messages reçus par un utilisateur
     */
    public static function getMessagesRecus(
        int $utilisateurId,
        bool $nonLusSeuls = false,
        int $limite = 50
    ): array {
        $conditions = ['destinataire_id' => $utilisateurId];

        if ($nonLusSeuls) {
            $conditions['lu'] = 0;
        }

        return MessageInterne::where($conditions);
    }

    /**
     * Retourne les messages envoyés par un utilisateur
     */
    public static function getMessagesEnvoyes(int $utilisateurId, int $limite = 50): array
    {
        return MessageInterne::where(['expediteur_id' => $utilisateurId]);
    }

    /**
     * Compte les messages non lus
     */
    public static function compterNonLus(int $utilisateurId): int
    {
        return MessageInterne::count([
            'destinataire_id' => $utilisateurId,
            'lu' => 0,
        ]);
    }

    /**
     * Retourne un message avec vérification d'accès
     */
    public static function getMessage(int $messageId, int $utilisateurId): ?MessageInterne
    {
        $message = MessageInterne::find($messageId);
        if ($message === null) {
            return null;
        }

        // Vérifier l'accès
        if ((int) $message->expediteur_id !== $utilisateurId && (int) $message->destinataire_id !== $utilisateurId) {
            return null;
        }

        return $message;
    }

    /**
     * Répond à un message
     */
    public static function repondre(
        int $messageOriginalId,
        int $utilisateurId,
        string $contenu
    ): MessageInterne {
        $original = MessageInterne::find($messageOriginalId);
        if ($original === null) {
            throw new NotFoundException('Message original non trouvé');
        }

        // L'utilisateur doit être le destinataire du message original
        if ((int) $original->destinataire_id !== $utilisateurId) {
            throw new NotFoundException('Message non trouvé');
        }

        $sujet = 'RE: ' . ($original->sujet ?? '');

        return self::envoyer(
            $utilisateurId,
            (int) $original->expediteur_id,
            $sujet,
            $contenu
        );
    }

    /**
     * Envoie un message à plusieurs destinataires
     */
    public static function envoyerMultiple(
        int $expediteurId,
        array $destinataireIds,
        string $sujet,
        string $contenu
    ): int {
        $count = 0;
        foreach ($destinataireIds as $destId) {
            try {
                self::envoyer($expediteurId, $destId, $sujet, $contenu);
                $count++;
            } catch (\Exception $e) {
                continue;
            }
        }
        return $count;
    }

    /**
     * Recherche dans les messages
     */
    public static function rechercher(int $utilisateurId, string $terme, int $limite = 50): array
    {
        $sql = "SELECT * FROM messages_internes 
                WHERE (destinataire_id = :uid OR expediteur_id = :uid2)
                AND (sujet LIKE :terme OR contenu LIKE :terme2)
                ORDER BY created_at DESC
                LIMIT :limite";

        $stmt = \App\Orm\Model::getConnection()->prepare($sql);
        $stmt->bindValue('uid', $utilisateurId, \PDO::PARAM_INT);
        $stmt->bindValue('uid2', $utilisateurId, \PDO::PARAM_INT);
        $stmt->bindValue('terme', "%{$terme}%", \PDO::PARAM_STR);
        $stmt->bindValue('terme2', "%{$terme}%", \PDO::PARAM_STR);
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
