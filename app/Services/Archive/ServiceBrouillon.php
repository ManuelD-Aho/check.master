<?php

declare(strict_types=1);

namespace App\Services\Archive;

use App\Models\HistoriqueEntite;
use App\Services\Security\ServiceAudit;
use App\Orm\Model;

/**
 * Service Brouillon
 * 
 * Gestion des brouillons de formulaires.
 * Sauvegarde automatique toutes les 30 secondes.
 * Conservation 7 jours par défaut.
 * 
 * @see PRD 06 - Documents & Archives
 */
class ServiceBrouillon
{
    /**
     * Durée de conservation par défaut (en jours)
     */
    private const EXPIRATION_JOURS = 7;

    /**
     * Sauvegarde ou met à jour un brouillon
     */
    public static function sauvegarder(
        int $utilisateurId,
        string $typeContexte,
        ?int $contexteId,
        string $codeFormulaire,
        array $donnees
    ): array {
        // Vérifier si un brouillon existe déjà
        $sql = "SELECT * FROM brouillons 
                WHERE utilisateur_id = :user
                AND type_contexte = :type
                AND (contexte_id = :ctx OR (contexte_id IS NULL AND :ctx2 IS NULL))
                AND code_formulaire = :code
                AND finalise = 0
                LIMIT 1";

        $stmt = Model::raw($sql, [
            'user' => $utilisateurId,
            'type' => $typeContexte,
            'ctx' => $contexteId,
            'ctx2' => $contexteId,
            'code' => $codeFormulaire,
        ]);

        $existant = $stmt->fetch(\PDO::FETCH_ASSOC);

        $expiration = date('Y-m-d H:i:s', strtotime('+' . self::EXPIRATION_JOURS . ' days'));

        if ($existant) {
            // Mettre à jour le brouillon existant
            $sql = "UPDATE brouillons SET 
                        donnees_json = :donnees,
                        date_modification = NOW(),
                        date_expiration = :exp
                    WHERE id_brouillon = :id";

            Model::raw($sql, [
                'donnees' => json_encode($donnees),
                'exp' => $expiration,
                'id' => $existant['id_brouillon'],
            ]);

            return [
                'id' => (int) $existant['id_brouillon'],
                'action' => 'updated',
                'expiration' => $expiration,
            ];
        }

        // Créer un nouveau brouillon
        $sql = "INSERT INTO brouillons 
                (utilisateur_id, type_contexte, contexte_id, code_formulaire, donnees_json, date_creation, date_modification, date_expiration, finalise)
                VALUES (:user, :type, :ctx, :code, :donnees, NOW(), NOW(), :exp, 0)";

        Model::raw($sql, [
            'user' => $utilisateurId,
            'type' => $typeContexte,
            'ctx' => $contexteId,
            'code' => $codeFormulaire,
            'donnees' => json_encode($donnees),
            'exp' => $expiration,
        ]);

        $id = (int) Model::getConnection()->lastInsertId();

        return [
            'id' => $id,
            'action' => 'created',
            'expiration' => $expiration,
        ];
    }

    /**
     * Récupère un brouillon
     */
    public static function recuperer(
        int $utilisateurId,
        string $typeContexte,
        ?int $contexteId,
        string $codeFormulaire
    ): ?array {
        $sql = "SELECT * FROM brouillons 
                WHERE utilisateur_id = :user
                AND type_contexte = :type
                AND (contexte_id = :ctx OR (contexte_id IS NULL AND :ctx2 IS NULL))
                AND code_formulaire = :code
                AND finalise = 0
                AND date_expiration > NOW()
                ORDER BY date_modification DESC
                LIMIT 1";

        $stmt = Model::raw($sql, [
            'user' => $utilisateurId,
            'type' => $typeContexte,
            'ctx' => $contexteId,
            'ctx2' => $contexteId,
            'code' => $codeFormulaire,
        ]);

        $brouillon = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$brouillon) {
            return null;
        }

        return [
            'id' => (int) $brouillon['id_brouillon'],
            'donnees' => json_decode($brouillon['donnees_json'] ?? '{}', true),
            'date_creation' => $brouillon['date_creation'],
            'date_modification' => $brouillon['date_modification'],
            'date_expiration' => $brouillon['date_expiration'],
        ];
    }

    /**
     * Finalise un brouillon (soumission du formulaire)
     */
    public static function finaliser(int $brouillonId): bool
    {
        $sql = "UPDATE brouillons SET finalise = 1, date_finalisation = NOW() WHERE id_brouillon = :id";
        Model::raw($sql, ['id' => $brouillonId]);
        return true;
    }

    /**
     * Supprime un brouillon
     */
    public static function supprimer(int $brouillonId, int $utilisateurId): bool
    {
        $sql = "DELETE FROM brouillons WHERE id_brouillon = :id AND utilisateur_id = :user";
        $stmt = Model::raw($sql, ['id' => $brouillonId, 'user' => $utilisateurId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Liste les brouillons d'un utilisateur
     */
    public static function listerBrouillons(int $utilisateurId): array
    {
        $sql = "SELECT id_brouillon, type_contexte, contexte_id, code_formulaire, 
                       date_creation, date_modification, date_expiration
                FROM brouillons 
                WHERE utilisateur_id = :user
                AND finalise = 0
                AND date_expiration > NOW()
                ORDER BY date_modification DESC";

        $stmt = Model::raw($sql, ['user' => $utilisateurId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Nettoie les brouillons expirés
     */
    public static function nettoyerExpires(): int
    {
        $sql = "DELETE FROM brouillons WHERE date_expiration < NOW()";
        $stmt = Model::raw($sql);
        $count = $stmt->rowCount();

        if ($count > 0) {
            ServiceAudit::log('nettoyage_brouillons', 'systeme', null, [
                'supprimes' => $count,
            ]);
        }

        return $count;
    }

    /**
     * Vérifie si un brouillon existe
     */
    public static function existe(
        int $utilisateurId,
        string $typeContexte,
        ?int $contexteId,
        string $codeFormulaire
    ): bool {
        return self::recuperer($utilisateurId, $typeContexte, $contexteId, $codeFormulaire) !== null;
    }
}
