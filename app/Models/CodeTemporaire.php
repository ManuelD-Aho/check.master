<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CodeTemporaire
 * 
 * Représente un code temporaire (vérification, reset password, président jury).
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
     * Durées par défaut (en secondes)
     */
    public const DUREE_PRESIDENT_JURY = 86400;  // 24h
    public const DUREE_RESET_PASSWORD = 3600;    // 1h
    public const DUREE_VERIFICATION = 600;       // 10min

    /**
     * Vérifie si le code est valide
     */
    public function estValide(): bool
    {
        if ($this->utilise) {
            return false;
        }

        $now = time();
        $valideDe = strtotime($this->valide_de);
        $valideJusqua = strtotime($this->valide_jusqu_a);

        return $now >= $valideDe && $now <= $valideJusqua;
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
     * Vérifie un code en clair contre le hash stocké
     */
    public function verifier(string $code): bool
    {
        if (!$this->estValide()) {
            return false;
        }
        return password_verify($code, $this->code_hash);
    }

    /**
     * Génère un code numérique aléatoire
     */
    public static function genererCode(int $longueur = 6): string
    {
        $code = '';
        for ($i = 0; $i < $longueur; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }

    /**
     * Crée un nouveau code temporaire
     */
    public static function creer(
        int $utilisateurId,
        string $type,
        ?int $soutenanceId = null,
        ?int $duree = null
    ): array {
        // Invalider les codes précédents du même type
        self::invaliderPrecedents($utilisateurId, $type);

        // Déterminer la durée
        $duree = $duree ?? match ($type) {
            self::TYPE_PRESIDENT_JURY => self::DUREE_PRESIDENT_JURY,
            self::TYPE_RESET_PASSWORD => self::DUREE_RESET_PASSWORD,
            self::TYPE_VERIFICATION => self::DUREE_VERIFICATION,
            default => 3600,
        };

        // Générer le code en clair et le hash
        $codeEnClair = self::genererCode();
        $codeHash = password_hash($codeEnClair, PASSWORD_ARGON2ID);

        $now = time();
        $code = new self([
            'utilisateur_id' => $utilisateurId,
            'soutenance_id' => $soutenanceId,
            'code_hash' => $codeHash,
            'type' => $type,
            'valide_de' => date('Y-m-d H:i:s', $now),
            'valide_jusqu_a' => date('Y-m-d H:i:s', $now + $duree),
            'utilise' => false,
        ]);
        $code->save();

        // Retourner le modèle ET le code en clair (pour envoi par email/SMS)
        return [
            'model' => $code,
            'code' => $codeEnClair,
        ];
    }

    /**
     * Invalide les codes précédents du même type pour un utilisateur
     */
    public static function invaliderPrecedents(int $utilisateurId, string $type): void
    {
        $codes = self::where([
            'utilisateur_id' => $utilisateurId,
            'type' => $type,
            'utilise' => false,
        ]);

        foreach ($codes as $code) {
            $code->utilise = true;
            $code->save();
        }
    }

    /**
     * Trouve un code valide pour un utilisateur et un type
     */
    public static function trouverValide(int $utilisateurId, string $type): ?self
    {
        $codes = self::where([
            'utilisateur_id' => $utilisateurId,
            'type' => $type,
            'utilise' => false,
        ]);

        foreach ($codes as $code) {
            if ($code->estValide()) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Retourne le temps restant avant expiration (en secondes)
     */
    public function tempsRestant(): int
    {
        $expire = strtotime($this->valide_jusqu_a);
        $remaining = $expire - time();
        return max(0, $remaining);
    }
}
