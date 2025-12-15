<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Salle
 * 
 * Représente une salle pour les soutenances.
 * Table: salles
 */
class Salle extends Model
{
    protected string $table = 'salles';
    protected string $primaryKey = 'id_salle';
    protected array $fillable = [
        'nom_salle',
        'capacite',
        'equipements',
        'actif',
    ];

    /**
     * Retourne les équipements sous forme de tableau
     */
    public function getEquipements(): array
    {
        if (empty($this->equipements)) {
            return [];
        }
        return json_decode($this->equipements, true) ?? [];
    }

    /**
     * Vérifie si la salle est disponible à une date/heure
     */
    public function estDisponible(string $date, string $heureDebut, string $heureFin): bool
    {
        $sql = "SELECT COUNT(*) FROM soutenances 
                WHERE salle_id = :id 
                AND date_soutenance = :date
                AND statut NOT IN ('Annulee', 'Reportee')
                AND (
                    (heure_debut < :fin AND heure_fin > :debut)
                )";

        $stmt = self::raw($sql, [
            'id' => $this->getId(),
            'date' => $date,
            'debut' => $heureDebut,
            'fin' => $heureFin,
        ]);

        return (int) $stmt->fetchColumn() === 0;
    }

    /**
     * Retourne les soutenances planifiées dans cette salle
     */
    public function getSoutenances(?string $date = null): array
    {
        $sql = "SELECT * FROM soutenances WHERE salle_id = :id";
        $params = ['id' => $this->getId()];

        if ($date !== null) {
            $sql .= " AND date_soutenance = :date";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY date_soutenance, heure_debut";

        $stmt = self::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Retourne toutes les salles actives
     *
     * @return self[]
     */
    public static function actives(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Trouve les salles disponibles pour un créneau
     *
     * @return self[]
     */
    public static function disponibles(string $date, string $heureDebut, string $heureFin): array
    {
        $salles = self::actives();
        return array_filter($salles, fn($s) => $s->estDisponible($date, $heureDebut, $heureFin));
    }
}
