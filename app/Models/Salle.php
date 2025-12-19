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
        'batiment',
        'capacite',
        'equipement_json',
        'actif',
    ];

    /**
     * Retourne les équipements sous forme de tableau
     */
    public function getEquipements(): array
    {
        if (empty($this->equipement_json)) {
            return [];
        }
        return json_decode($this->equipement_json, true) ?? [];
    }

    /**
     * Vérifie si la salle est disponible à une date/heure
     */
    public function estDisponible(\DateTime $dateHeure, int $dureeMinutes = 60): bool
    {
        $debut = $dateHeure->format('Y-m-d H:i:s');
        $fin = (clone $dateHeure)->add(new \DateInterval('PT' . $dureeMinutes . 'M'))->format('Y-m-d H:i:s');

        $sql = "SELECT COUNT(*) FROM soutenances 
                WHERE salle_id = :id 
                AND statut NOT IN ('Annulee', 'Reportee')
                AND (
                    (date_soutenance < :fin AND DATE_ADD(date_soutenance, INTERVAL duree_minutes MINUTE) > :debut)
                )";

        $stmt = self::raw($sql, [
            'id' => $this->getId(),
            'debut' => $debut,
            'fin' => $fin,
        ]);

        return (int) $stmt->fetchColumn() === 0;
    }

    /**
     * Retourne les soutenances planifiées dans cette salle
     */
    public function getSoutenances(?string $dateStr = null): array
    {
        $sql = "SELECT * FROM soutenances WHERE salle_id = :id";
        $params = ['id' => $this->getId()];

        if ($dateStr !== null) {
            $sql .= " AND DATE(date_soutenance) = :date";
            $params['date'] = $dateStr;
        }

        $sql .= " ORDER BY date_soutenance";

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
    public static function disponibles(\DateTime $dateHeure, int $dureeMinutes = 60): array
    {
        $salles = self::actives();
        return array_filter($salles, fn($s) => $s->estDisponible($dateHeure, $dureeMinutes));
    }
}
