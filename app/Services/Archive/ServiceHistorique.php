<?php

declare(strict_types=1);

namespace App\Services\Archive;

use App\Models\HistoriqueEntite;
use App\Services\Security\ServiceAudit;
use App\Orm\Model;

/**
 * Service Historique
 * 
 * Historisation des modifications d'entités.
 * Snapshots JSON, comparaison de versions.
 * 
 * @see PRD 06 - Documents & Archives
 */
class ServiceHistorique
{
    /**
     * Enregistre un snapshot d'une entité
     */
    public static function enregistrerVersion(
        string $typeEntite,
        int $entiteId,
        array $donnees,
        int $modifiePar,
        ?string $commentaire = null
    ): HistoriqueEntite {
        // Récupérer la dernière version
        $sql = "SELECT MAX(numero_version) as max_version 
                FROM historique_entites 
                WHERE type_entite = :type AND entite_id = :id";

        $stmt = Model::raw($sql, ['type' => $typeEntite, 'id' => $entiteId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $derniereVersion = (int) ($result['max_version'] ?? 0);

        $nouvelleVersion = $derniereVersion + 1;

        $historique = new HistoriqueEntite([
            'type_entite' => $typeEntite,
            'entite_id' => $entiteId,
            'numero_version' => $nouvelleVersion,
            'snapshot_json' => json_encode($donnees),
            'modifie_par' => $modifiePar,
            'date_modification' => date('Y-m-d H:i:s'),
            'commentaire' => $commentaire,
        ]);
        $historique->save();

        return $historique;
    }

    /**
     * Retourne l'historique complet d'une entité
     */
    public static function getHistorique(string $typeEntite, int $entiteId): array
    {
        $sql = "SELECT he.*, u.login_utilisateur as modifie_par_email,
                       COALESCE(e.nom_etu, ens.nom_ens, pa.nom_pa) as modifie_par_nom,
                       COALESCE(e.prenom_etu, ens.prenom_ens, pa.prenom_pa) as modifie_par_prenom
                FROM historique_entites he
                LEFT JOIN utilisateurs u ON u.id_utilisateur = he.modifie_par
                LEFT JOIN etudiants e ON e.utilisateur_id = he.modifie_par
                LEFT JOIN enseignants ens ON ens.utilisateur_id = he.modifie_par
                LEFT JOIN personnel_admin pa ON pa.utilisateur_id = he.modifie_par
                WHERE he.type_entite = :type AND he.entite_id = :id
                ORDER BY he.numero_version DESC";

        $stmt = Model::raw($sql, ['type' => $typeEntite, 'id' => $entiteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne une version spécifique
     */
    public static function getVersion(string $typeEntite, int $entiteId, int $version): ?array
    {
        $sql = "SELECT * FROM historique_entites 
                WHERE type_entite = :type AND entite_id = :id AND numero_version = :version";

        $stmt = Model::raw($sql, [
            'type' => $typeEntite,
            'id' => $entiteId,
            'version' => $version,
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        $result['snapshot'] = json_decode($result['snapshot_json'] ?? '{}', true);
        return $result;
    }

    /**
     * Retourne la dernière version d'une entité
     */
    public static function getDerniereVersion(string $typeEntite, int $entiteId): ?array
    {
        $sql = "SELECT * FROM historique_entites 
                WHERE type_entite = :type AND entite_id = :id
                ORDER BY numero_version DESC
                LIMIT 1";

        $stmt = Model::raw($sql, ['type' => $typeEntite, 'id' => $entiteId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $result['snapshot'] = json_decode($result['snapshot_json'] ?? '{}', true);
        return $result;
    }

    /**
     * Compare deux versions d'une entité
     */
    public static function comparer(string $typeEntite, int $entiteId, int $version1, int $version2): array
    {
        $v1 = self::getVersion($typeEntite, $entiteId, $version1);
        $v2 = self::getVersion($typeEntite, $entiteId, $version2);

        if ($v1 === null || $v2 === null) {
            return ['erreur' => 'Version non trouvée'];
        }

        $snapshot1 = $v1['snapshot'] ?? [];
        $snapshot2 = $v2['snapshot'] ?? [];

        $diff = [
            'version_1' => $version1,
            'version_2' => $version2,
            'ajoutes' => [],
            'supprimes' => [],
            'modifies' => [],
        ];

        // Trouver les champs ajoutés ou modifiés
        foreach ($snapshot2 as $key => $value) {
            if (!array_key_exists($key, $snapshot1)) {
                $diff['ajoutes'][$key] = $value;
            } elseif ($snapshot1[$key] !== $value) {
                $diff['modifies'][$key] = [
                    'avant' => $snapshot1[$key],
                    'apres' => $value,
                ];
            }
        }

        // Trouver les champs supprimés
        foreach ($snapshot1 as $key => $value) {
            if (!array_key_exists($key, $snapshot2)) {
                $diff['supprimes'][$key] = $value;
            }
        }

        return $diff;
    }

    /**
     * Restaure une version antérieure
     */
    public static function restaurer(
        string $typeEntite,
        int $entiteId,
        int $version,
        int $restaurePar
    ): bool {
        $versionData = self::getVersion($typeEntite, $entiteId, $version);
        if ($versionData === null) {
            return false;
        }

        $snapshot = $versionData['snapshot'] ?? [];

        // Cette méthode dépend du type d'entité
        // On enregistre une nouvelle version avec les anciennes données
        self::enregistrerVersion(
            $typeEntite,
            $entiteId,
            $snapshot,
            $restaurePar,
            "Restauration de la version {$version}"
        );

        ServiceAudit::log('restauration_version', $typeEntite, $entiteId, [
            'version_restauree' => $version,
        ]);

        return true;
    }

    /**
     * Retourne le nombre de versions d'une entité
     */
    public static function compterVersions(string $typeEntite, int $entiteId): int
    {
        $sql = "SELECT COUNT(*) FROM historique_entites 
                WHERE type_entite = :type AND entite_id = :id";

        $stmt = Model::raw($sql, ['type' => $typeEntite, 'id' => $entiteId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Exporte l'historique complet d'une entité
     */
    public static function exporter(string $typeEntite, int $entiteId): array
    {
        return [
            'type_entite' => $typeEntite,
            'entite_id' => $entiteId,
            'date_export' => date('Y-m-d H:i:s'),
            'versions' => self::getHistorique($typeEntite, $entiteId),
        ];
    }

    /**
     * Nettoie les versions anciennes (garde les N dernières)
     */
    public static function nettoyerAnciennes(string $typeEntite, int $entiteId, int $garder = 10): int
    {
        $sql = "DELETE FROM historique_entites 
                WHERE type_entite = :type AND entite_id = :id
                AND numero_version NOT IN (
                    SELECT numero_version FROM (
                        SELECT numero_version FROM historique_entites 
                        WHERE type_entite = :type2 AND entite_id = :id2
                        ORDER BY numero_version DESC
                        LIMIT :garder
                    ) AS versions_recentes
                )";

        $stmt = Model::raw($sql, [
            'type' => $typeEntite,
            'id' => $entiteId,
            'type2' => $typeEntite,
            'id2' => $entiteId,
            'garder' => $garder,
        ]);

        return $stmt->rowCount();
    }
}
