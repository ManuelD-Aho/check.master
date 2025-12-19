<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle RoleTemporaire
 * 
 * Gère les rôles temporaires (ex: Président Jury le jour J).
 * Table: roles_temporaires
 */
class RoleTemporaire extends Model
{
    protected string $table = 'roles_temporaires';
    protected string $primaryKey = 'id_role_temp';
    protected array $fillable = [
        'utilisateur_id',
        'role_code',
        'contexte_type',
        'contexte_id',
        'permissions_json',
        'actif',
        'valide_de',
        'valide_jusqu_a',
        'cree_par',
    ];

    /**
     * Codes de rôles temporaires
     */
    public const ROLE_PRESIDENT_JURY = 'president_jury';
    public const ROLE_MEMBRE_JURY = 'membre_jury';
    public const ROLE_SUPPLEANT = 'suppleant';

    /**
     * Types de contexte
     */
    public const CONTEXTE_SOUTENANCE = 'soutenance';
    public const CONTEXTE_SESSION = 'session';

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur associé
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    /**
     * Retourne l'utilisateur créateur
     */
    public function creePar(): ?Utilisateur
    {
        if ($this->cree_par === null) {
            return null;
        }
        return Utilisateur::find((int) $this->cree_par);
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve les rôles temporaires actifs d'un utilisateur
     * @return self[]
     */
    public static function actifsPourUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT * FROM roles_temporaires 
                WHERE utilisateur_id = :id 
                AND actif = 1 
                AND valide_de <= NOW() 
                AND valide_jusqu_a >= NOW()";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Trouve les rôles pour un contexte donné
     * @return self[]
     */
    public static function pourContexte(string $contexteType, int $contexteId): array
    {
        return self::where([
            'contexte_type' => $contexteType,
            'contexte_id' => $contexteId,
            'actif' => true,
        ]);
    }

    /**
     * Trouve un rôle spécifique pour un utilisateur et un contexte
     */
    public static function trouver(
        int $utilisateurId,
        string $roleCode,
        ?string $contexteType = null,
        ?int $contexteId = null
    ): ?self {
        $sql = "SELECT * FROM roles_temporaires 
                WHERE utilisateur_id = :uid 
                AND role_code = :role 
                AND actif = 1 
                AND valide_de <= NOW() 
                AND valide_jusqu_a >= NOW()";

        $params = ['uid' => $utilisateurId, 'role' => $roleCode];

        if ($contexteType !== null) {
            $sql .= " AND contexte_type = :ctype";
            $params['ctype'] = $contexteType;
        }

        if ($contexteId !== null) {
            $sql .= " AND contexte_id = :cid";
            $params['cid'] = $contexteId;
        }

        $sql .= " LIMIT 1";

        $stmt = self::raw($sql, $params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le rôle est actif
     */
    public function estActif(): bool
    {
        if (!$this->actif) {
            return false;
        }

        $now = time();
        $valideFrom = strtotime($this->valide_de);
        $valideTo = strtotime($this->valide_jusqu_a);

        return $now >= $valideFrom && $now <= $valideTo;
    }

    /**
     * Vérifie si le rôle est expiré
     */
    public function estExpire(): bool
    {
        return strtotime($this->valide_jusqu_a) < time();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne les permissions JSON décodées
     */
    public function getPermissions(): array
    {
        if (empty($this->permissions_json)) {
            return [];
        }
        return json_decode($this->permissions_json, true) ?? [];
    }

    /**
     * Définit les permissions JSON
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions_json = json_encode($permissions);
    }

    /**
     * Vérifie si le rôle a une permission spécifique
     */
    public function aPermission(string $ressource, string $action): bool
    {
        $permissions = $this->getPermissions();
        return isset($permissions[$ressource][$action]) && $permissions[$ressource][$action] === true;
    }

    /**
     * Crée un rôle temporaire de président de jury
     */
    public static function creerPresidentJury(
        int $utilisateurId,
        int $soutenanceId,
        \DateTime $dateSoutenance,
        ?int $creeParId = null
    ): self {
        $jour = $dateSoutenance->format('Y-m-d');

        $permissions = [
            'soutenance' => [
                'voir' => true,
                'saisir_notes' => true,
                'valider_notes' => true,
                'voir_grille' => true,
            ],
            'etudiant' => [
                'voir_dossier' => true,
            ],
        ];

        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'role_code' => self::ROLE_PRESIDENT_JURY,
            'contexte_type' => self::CONTEXTE_SOUTENANCE,
            'contexte_id' => $soutenanceId,
            'permissions_json' => json_encode($permissions),
            'actif' => true,
            'valide_de' => $jour . ' 00:00:00',
            'valide_jusqu_a' => $jour . ' 23:59:59',
            'cree_par' => $creeParId,
        ]);
        $model->save();

        return $model;
    }

    /**
     * Crée un rôle temporaire de membre de jury
     */
    public static function creerMembreJury(
        int $utilisateurId,
        int $soutenanceId,
        \DateTime $dateSoutenance,
        ?int $creeParId = null
    ): self {
        $jour = $dateSoutenance->format('Y-m-d');

        $permissions = [
            'soutenance' => [
                'voir' => true,
                'saisir_notes' => true,
            ],
            'etudiant' => [
                'voir_dossier' => true,
            ],
        ];

        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'role_code' => self::ROLE_MEMBRE_JURY,
            'contexte_type' => self::CONTEXTE_SOUTENANCE,
            'contexte_id' => $soutenanceId,
            'permissions_json' => json_encode($permissions),
            'actif' => true,
            'valide_de' => $jour . ' 00:00:00',
            'valide_jusqu_a' => $jour . ' 23:59:59',
            'cree_par' => $creeParId,
        ]);
        $model->save();

        return $model;
    }

    /**
     * Désactive le rôle temporaire
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }

    /**
     * Prolonge le rôle temporaire
     */
    public function prolonger(\DateTime $nouvelleFinValidite): void
    {
        $this->valide_jusqu_a = $nouvelleFinValidite->format('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Supprime les rôles expirés
     */
    public static function nettoyerExpires(): int
    {
        $sql = "DELETE FROM roles_temporaires WHERE valide_jusqu_a < NOW()";
        $stmt = self::raw($sql, []);
        return $stmt->rowCount();
    }
}
