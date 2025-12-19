<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CodeTemporaire
 * 
 * Gère les codes temporaires (président jury, reset password, vérification).
 * Table: codes_temporaires
 */
class CodeTemporaire extends Model
{
    protected string $table = 'codes_temporaires';
    protected string $primaryKey = 'id_code';
    protected array $fillable = [
        'utilisateur_id',
        'soutenance_id',
        'code_hash',
        'type',
        'valide_de',
        'valide_jusqu_a',
        'utilise',
        'utilise_a',
    ];

    /**
     * Types de codes
     */
    public const TYPE_PRESIDENT_JURY = 'president_jury';
    public const TYPE_RESET_PASSWORD = 'reset_password';
    public const TYPE_VERIFICATION = 'verification';

    /**
     * Caractères pour génération de code (sans ambiguïté 0/O, 1/I)
     */
    private const CARACTERES_CODE = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur associé
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    /**
     * Retourne la soutenance associée (si applicable)
     */
    public function soutenance(): ?Soutenance
    {
        if ($this->soutenance_id === null) {
            return null;
        }
        return $this->belongsTo(Soutenance::class, 'soutenance_id', 'id_soutenance');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un code valide par son hash
     */
    public static function findByHash(string $codeHash, string $type): ?self
    {
        $sql = "SELECT * FROM codes_temporaires 
                WHERE code_hash = :hash 
                AND type = :type 
                AND utilise = 0 
                AND valide_de <= NOW() 
                AND valide_jusqu_a >= NOW()
                LIMIT 1";

        $stmt = self::raw($sql, ['hash' => $codeHash, 'type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Trouve les codes actifs d'un utilisateur
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId, ?string $type = null): array
    {
        $conditions = ['utilisateur_id' => $utilisateurId];
        if ($type !== null) {
            $conditions['type'] = $type;
        }
        return self::where($conditions);
    }

    /**
     * Trouve le code actif pour une soutenance
     */
    public static function pourSoutenance(int $soutenanceId): ?self
    {
        $sql = "SELECT * FROM codes_temporaires 
                WHERE soutenance_id = :id 
                AND type = :type 
                AND utilise = 0 
                AND valide_de <= NOW() 
                AND valide_jusqu_a >= NOW()
                LIMIT 1";

        $stmt = self::raw($sql, [
            'id' => $soutenanceId,
            'type' => self::TYPE_PRESIDENT_JURY,
        ]);
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
     * Vérifie si le code est valide
     */
    public function estValide(): bool
    {
        if ($this->utilise) {
            return false;
        }

        $now = time();
        $valideFrom = strtotime($this->valide_de);
        $valideTo = strtotime($this->valide_jusqu_a);

        return $now >= $valideFrom && $now <= $valideTo;
    }

    /**
     * Vérifie si le code a été utilisé
     */
    public function estUtilise(): bool
    {
        return (bool) $this->utilise;
    }

    /**
     * Vérifie si le code est expiré
     */
    public function estExpire(): bool
    {
        return strtotime($this->valide_jusqu_a) < time();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Génère un code aléatoire
     */
    public static function genererCode(int $longueur = 8): string
    {
        $code = '';
        $max = strlen(self::CARACTERES_CODE) - 1;

        for ($i = 0; $i < $longueur; $i++) {
            $code .= self::CARACTERES_CODE[random_int(0, $max)];
        }

        return $code;
    }

    /**
     * Hash un code
     */
    public static function hasherCode(string $code): string
    {
        return password_hash($code, PASSWORD_ARGON2ID);
    }

    /**
     * Vérifie un code contre son hash
     */
    public function verifierCode(string $code): bool
    {
        return password_verify($code, $this->code_hash);
    }

    /**
     * Crée un code pour président de jury
     */
    public static function creerPourPresidentJury(
        int $utilisateurId,
        int $soutenanceId,
        \DateTime $dateSoutenance
    ): array {
        // Code valide de 06:00 à 23:59 le jour de la soutenance
        $jour = $dateSoutenance->format('Y-m-d');
        $valideDe = $jour . ' 06:00:00';
        $valideJusqua = $jour . ' 23:59:59';

        // Invalider les anciens codes pour cette soutenance
        $sql = "UPDATE codes_temporaires 
                SET utilise = 1 
                WHERE soutenance_id = :id AND type = :type AND utilise = 0";
        self::raw($sql, [
            'id' => $soutenanceId,
            'type' => self::TYPE_PRESIDENT_JURY,
        ]);

        $code = self::genererCode(8);
        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'soutenance_id' => $soutenanceId,
            'code_hash' => self::hasherCode($code),
            'type' => self::TYPE_PRESIDENT_JURY,
            'valide_de' => $valideDe,
            'valide_jusqu_a' => $valideJusqua,
            'utilise' => false,
        ]);
        $model->save();

        return ['code' => $code, 'model' => $model];
    }

    /**
     * Crée un code de réinitialisation de mot de passe (valide 1 heure)
     */
    public static function creerPourResetPassword(int $utilisateurId): array
    {
        // Invalider les anciens codes
        $sql = "UPDATE codes_temporaires 
                SET utilise = 1 
                WHERE utilisateur_id = :id AND type = :type AND utilise = 0";
        self::raw($sql, [
            'id' => $utilisateurId,
            'type' => self::TYPE_RESET_PASSWORD,
        ]);

        $code = self::genererCode(32);
        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'code_hash' => self::hasherCode($code),
            'type' => self::TYPE_RESET_PASSWORD,
            'valide_de' => date('Y-m-d H:i:s'),
            'valide_jusqu_a' => date('Y-m-d H:i:s', time() + 3600), // 1 heure
            'utilise' => false,
        ]);
        $model->save();

        return ['code' => $code, 'model' => $model];
    }

    /**
     * Crée un code de vérification (valide 24 heures)
     */
    public static function creerPourVerification(int $utilisateurId): array
    {
        $code = self::genererCode(6);
        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'code_hash' => self::hasherCode($code),
            'type' => self::TYPE_VERIFICATION,
            'valide_de' => date('Y-m-d H:i:s'),
            'valide_jusqu_a' => date('Y-m-d H:i:s', time() + 86400), // 24 heures
            'utilise' => false,
        ]);
        $model->save();

        return ['code' => $code, 'model' => $model];
    }

    /**
     * Marque le code comme utilisé
     */
    public function marquerUtilise(): void
    {
        $this->utilise = true;
        $this->utilise_a = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Supprime les codes expirés
     */
    public static function nettoyerExpires(): int
    {
        $sql = "DELETE FROM codes_temporaires WHERE valide_jusqu_a < NOW()";
        $stmt = self::raw($sql, []);
        return $stmt->rowCount();
    }
}
