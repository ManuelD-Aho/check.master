<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Mention
 * 
 * Représente une mention académique basée sur la note.
 * Table: mentions
 */
class Mention extends Model
{
    protected string $table = 'mentions';
    protected string $primaryKey = 'id_mention';
    protected array $fillable = [
        'code_mention',
        'libelle_mention',
        'note_min',
        'note_max',
        'ordre_affichage',
    ];

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve la mention par code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_mention' => $code]);
    }

    /**
     * Trouve la mention correspondant à une note
     */
    public static function trouverPourNote(float $note): ?self
    {
        $sql = "SELECT * FROM mentions 
                WHERE note_min <= :note AND note_max >= :note 
                LIMIT 1";

        $stmt = self::raw($sql, ['note' => $note]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne toutes les mentions triées par ordre d'affichage
     * @return self[]
     */
    public static function triesParOrdre(): array
    {
        $sql = "SELECT * FROM mentions ORDER BY ordre_affichage ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée une nouvelle mention
     */
    public static function creer(
        string $code,
        string $libelle,
        float $noteMin,
        float $noteMax,
        int $ordre
    ): self {
        $mention = new self([
            'code_mention' => $code,
            'libelle_mention' => $libelle,
            'note_min' => $noteMin,
            'note_max' => $noteMax,
            'ordre_affichage' => $ordre,
        ]);
        $mention->save();
        return $mention;
    }

    /**
     * Vérifie si une note est dans la plage de cette mention
     */
    public function contientNote(float $note): bool
    {
        return $note >= (float) $this->note_min && $note <= (float) $this->note_max;
    }

    /**
     * Retourne le libellé formaté pour affichage
     */
    public function getLibelleComplet(): string
    {
        return sprintf(
            '%s (%.2f - %.2f)',
            $this->libelle_mention,
            (float) $this->note_min,
            (float) $this->note_max
        );
    }
}
