<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Code Temporaire
 * 
 * Représente un code d'accès temporaire (ex: Président Jury).
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
     * Types de codes temporaires
     */
    public const TYPE_PRESIDENT_JURY = 'president_jury';
    public const TYPE_RESET_PASSWORD = 'reset_password';
    public const TYPE_VERIFICATION = 'verification';

    /**
     * Caractères autorisés pour génération (sans 0/O, 1/I pour éviter confusion)
     */
    private const CHARS_AUTORISES = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Génère un code aléatoire de 8 caractères
     */
    public static function genererCode(): string
    {
        $code = '';
        $max = strlen(self::CHARS_AUTORISES) - 1;

        for ($i = 0; $i < 8; $i++) {
            $code .= self::CHARS_AUTORISES[random_int(0, $max)];
        }

        return $code;
    }

    /**
     * Hash un code pour stockage sécurisé
     */
    public static function hasherCode(string $code): string
    {
        return password_hash($code, PASSWORD_ARGON2ID);
    }

    /**
     * Vérifie un code contre son hash
     */
    public static function verifierCode(string $code, string $hash): bool
    {
        return password_verify($code, $hash);
    }

    /**
     * Vérifie si le code est dans sa période de validité
     */
    public function estValide(): bool
    {
        $now = time();
        $debut = strtotime($this->valide_de);
        $fin = strtotime($this->valide_jusqu_a);

        return $now >= $debut && $now <= $fin && !$this->utilise;
    }

    /**
     * Marque le code comme utilisé
     */
    public function marquerUtilise(): void
    {
        $this->utilise = true;
        $this->utilise_a = date('Y-m-d H:i:s');
    }

    /**
     * Trouve un code valide par utilisateur et type
     */
    public static function findCodeValide(int $userId, string $type): ?self
    {
        $results = self::where([
            'utilisateur_id' => $userId,
            'type' => $type,
            'utilise' => 0,
        ]);

        foreach ($results as $code) {
            if ($code->estValide()) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Révoque tous les codes expirés
     */
    public static function revoquerExpires(): int
    {
        $sql = "UPDATE codes_temporaires SET utilise = 1 
                WHERE utilise = 0 AND valide_jusqu_a < NOW()";
        $stmt = self::raw($sql);
        return $stmt->rowCount();
    }
}
