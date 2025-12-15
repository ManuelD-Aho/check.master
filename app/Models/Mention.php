<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Mention
 * 
 * Mention de soutenance ou de diplôme.
 * Table: mentions
 */
class Mention extends Model
{
    protected string $table = 'mentions';
    protected string $primaryKey = 'id_mention';
    protected array $fillable = [
        'libelle',
        'note_min',
        'note_max',
        'description',
    ];

    public static function trouverPourNote(float $note): ?self
    {
        // Logique complexe en SQL brut car ORM simple
        $sql = "SELECT * FROM mentions WHERE note_min <= :note AND note_max >= :note LIMIT 1";
        $stmt = self::raw($sql, ['note' => $note]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new self($row) : null;
    }
}
