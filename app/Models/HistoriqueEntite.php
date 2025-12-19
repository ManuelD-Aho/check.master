<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle HistoriqueEntite
 * 
 * Stocke les snapshots JSON des entités pour historisation/rollback.
 * Table: historique_entites
 */
class HistoriqueEntite extends Model
{
    protected string $table = 'historique_entites';
    protected string $primaryKey = 'id_historique';
    protected array $fillable = [
        'entite_type',
        'entite_id',
        'version',
        'snapshot_json',
        'modifie_par',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur ayant effectué la modification
     */
    public function modifiePar(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'modifie_par', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne l'historique d'une entité
     * @return self[]
     */
    public static function pourEntite(string $type, int $id): array
    {
        $sql = "SELECT * FROM historique_entites 
                WHERE entite_type = :type AND entite_id = :id 
                ORDER BY version DESC";

        $stmt = self::raw($sql, ['type' => $type, 'id' => $id]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne la dernière version d'une entité
     */
    public static function derniereVersion(string $type, int $id): ?self
    {
        $sql = "SELECT * FROM historique_entites 
                WHERE entite_type = :type AND entite_id = :id 
                ORDER BY version DESC
                LIMIT 1";

        $stmt = self::raw($sql, ['type' => $type, 'id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne le numéro de la prochaine version
     */
    public static function prochaineVersion(string $type, int $id): int
    {
        $derniere = self::derniereVersion($type, $id);
        return $derniere ? (int) $derniere->version + 1 : 1;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un snapshot d'une entité
     */
    public static function creerSnapshot(
        string $type,
        int $id,
        array $donnees,
        ?int $modifiePar = null
    ): self {
        $historique = new self([
            'entite_type' => $type,
            'entite_id' => $id,
            'version' => self::prochaineVersion($type, $id),
            'snapshot_json' => json_encode($donnees),
            'modifie_par' => $modifiePar,
        ]);
        $historique->save();
        return $historique;
    }

    /**
     * Retourne le snapshot décodé
     */
    public function getSnapshot(): array
    {
        if (empty($this->snapshot_json)) {
            return [];
        }
        return json_decode($this->snapshot_json, true) ?? [];
    }

    /**
     * Compare deux versions et retourne les différences
     */
    public static function comparer(string $type, int $id, int $version1, int $version2): array
    {
        $historiques = self::pourEntite($type, $id);
        $v1 = null;
        $v2 = null;

        foreach ($historiques as $h) {
            if ((int) $h->version === $version1) {
                $v1 = $h->getSnapshot();
            }
            if ((int) $h->version === $version2) {
                $v2 = $h->getSnapshot();
            }
        }

        if ($v1 === null || $v2 === null) {
            return [];
        }

        $differences = [];
        $toutesLesCles = array_unique(array_merge(array_keys($v1), array_keys($v2)));

        foreach ($toutesLesCles as $cle) {
            $valeur1 = $v1[$cle] ?? null;
            $valeur2 = $v2[$cle] ?? null;

            if ($valeur1 !== $valeur2) {
                $differences[$cle] = [
                    'version_' . $version1 => $valeur1,
                    'version_' . $version2 => $valeur2,
                ];
            }
        }

        return $differences;
    }

    /**
     * Retourne l'historique récent (toutes entités)
     * @return self[]
     */
    public static function recent(int $limit = 50): array
    {
        $sql = "SELECT * FROM historique_entites 
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }
}
