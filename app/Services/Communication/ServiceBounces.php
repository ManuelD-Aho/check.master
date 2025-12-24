<?php

declare(strict_types=1);

namespace App\Services\Communication;

use App\Models\EmailBounce;
use App\Services\Security\ServiceAudit;

/**
 * Service Bounces
 * 
 * Gestion des rebonds d'emails (bounces).
 * Suivi des hard/soft bounces et blocage automatique.
 * 
 * @see Constitution III - Sécurité Par Défaut
 */
class ServiceBounces
{
    /**
     * Seuil de soft bounces avant blocage
     */
    private const SEUIL_SOFT_BOUNCE = 5;

    /**
     * Enregistre un échec d'envoi (bounce)
     */
    public static function enregistrerEchec(
        string $email,
        string $messageErreur,
        bool $hardBounce = false
    ): void {
        // Vérifier si un enregistrement existe déjà
        $bounce = EmailBounce::firstWhere(['email' => $email]);

        if ($bounce === null) {
            $bounce = new EmailBounce([
                'email' => $email,
                'type_bounce' => $hardBounce ? 'hard' : 'soft',
                'compteur_soft' => $hardBounce ? 0 : 1,
                'dernier_message' => $messageErreur,
                'bloque' => $hardBounce,
            ]);
        } else {
            if ($hardBounce) {
                $bounce->type_bounce = 'hard';
                $bounce->bloque = true;
            } else {
                $bounce->compteur_soft = ((int) $bounce->compteur_soft) + 1;
                $bounce->type_bounce = 'soft';

                // Bloquer après le seuil de soft bounces
                if ($bounce->compteur_soft >= self::SEUIL_SOFT_BOUNCE) {
                    $bounce->bloque = true;
                }
            }
            $bounce->dernier_message = $messageErreur;
            $bounce->derniere_occurrence = date('Y-m-d H:i:s');
        }

        $bounce->save();

        ServiceAudit::log('email_bounce', 'email', null, [
            'email' => $email,
            'type' => $hardBounce ? 'hard' : 'soft',
            'bloque' => $bounce->bloque,
        ]);
    }

    /**
     * Enregistre un hard bounce
     */
    public static function enregistrerHardBounce(string $email, string $messageErreur): void
    {
        self::enregistrerEchec($email, $messageErreur, true);
    }

    /**
     * Enregistre un soft bounce
     */
    public static function enregistrerSoftBounce(string $email, string $messageErreur): void
    {
        self::enregistrerEchec($email, $messageErreur, false);
    }

    /**
     * Vérifie si une adresse email est bloquée
     */
    public static function estBloque(string $email): bool
    {
        $bounce = EmailBounce::firstWhere(['email' => $email]);
        return $bounce !== null && (bool) $bounce->bloque;
    }

    /**
     * Débloque une adresse email
     */
    public static function debloquer(string $email): bool
    {
        $bounce = EmailBounce::firstWhere(['email' => $email]);
        if ($bounce === null) {
            return false;
        }

        $bounce->bloque = false;
        $bounce->compteur_soft = 0;
        $result = $bounce->save();

        if ($result) {
            ServiceAudit::log('deblocage_email', 'email', null, [
                'email' => $email,
            ]);
        }

        return $result;
    }

    /**
     * Retourne les informations de bounce pour une adresse
     */
    public static function getInfos(string $email): ?array
    {
        $bounce = EmailBounce::firstWhere(['email' => $email]);
        if ($bounce === null) {
            return null;
        }

        return [
            'email' => $email,
            'type_bounce' => $bounce->type_bounce,
            'compteur_soft' => $bounce->compteur_soft,
            'bloque' => (bool) $bounce->bloque,
            'dernier_message' => $bounce->dernier_message,
            'derniere_occurrence' => $bounce->derniere_occurrence,
        ];
    }

    /**
     * Retourne toutes les adresses bloquées
     */
    public static function getAdressesBloquees(): array
    {
        return EmailBounce::where(['bloque' => true]);
    }

    /**
     * Retourne les statistiques de bounces
     */
    public static function getStatistiques(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN bloque = 1 THEN 1 ELSE 0 END) as bloquees,
                    SUM(CASE WHEN type_bounce = 'hard' THEN 1 ELSE 0 END) as hard_bounces,
                    SUM(CASE WHEN type_bounce = 'soft' THEN 1 ELSE 0 END) as soft_bounces
                FROM email_bounces";

        $stmt = \App\Orm\Model::raw($sql);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [
            'total' => 0,
            'bloquees' => 0,
            'hard_bounces' => 0,
            'soft_bounces' => 0,
        ];
    }

    /**
     * Nettoie les anciens soft bounces non bloqués
     */
    public static function nettoyer(int $joursRetention = 90): int
    {
        $sql = "DELETE FROM email_bounces 
                WHERE bloque = 0 
                AND type_bounce = 'soft' 
                AND derniere_occurrence < DATE_SUB(NOW(), INTERVAL :jours DAY)";

        $stmt = \App\Orm\Model::getConnection()->prepare($sql);
        $stmt->bindValue('jours', $joursRetention, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Réinitialise le compteur de soft bounces pour une adresse
     */
    public static function reinitialiserCompteur(string $email): bool
    {
        $bounce = EmailBounce::firstWhere(['email' => $email]);
        if ($bounce === null) {
            return false;
        }

        $bounce->compteur_soft = 0;
        return $bounce->save();
    }
}
