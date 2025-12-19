<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle MaintenanceMode
 * 
 * Gestion du mode maintenance de l'application.
 * Table: maintenance_mode
 */
class MaintenanceMode extends Model
{
    protected string $table = 'maintenance_mode';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'actif',
        'message',
        'debut_maintenance',
        'fin_maintenance',
    ];

    /**
     * Messages par défaut
     */
    public const MESSAGE_DEFAUT = 'Le système est en maintenance. Veuillez réessayer plus tard.';
    public const MESSAGE_MISE_A_JOUR = 'Une mise à jour est en cours. Le système sera disponible dans quelques minutes.';

    // ===== MÉTHODES DE VÉRIFICATION =====

    /**
     * Vérifie si le mode maintenance est actif
     */
    public static function estActif(): bool
    {
        $sql = "SELECT * FROM maintenance_mode WHERE actif = 1 LIMIT 1";
        $stmt = self::raw($sql);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return false;
        }

        // Vérifier les dates si définies
        if (!empty($row['fin_maintenance'])) {
            if (strtotime($row['fin_maintenance']) < time()) {
                // La maintenance est terminée, la désactiver
                $sql = "UPDATE maintenance_mode SET actif = 0 WHERE id = :id";
                self::raw($sql, ['id' => $row['id']]);
                return false;
            }
        }

        if (!empty($row['debut_maintenance'])) {
            if (strtotime($row['debut_maintenance']) > time()) {
                // La maintenance n'a pas encore commencé
                return false;
            }
        }

        return true;
    }

    /**
     * Retourne le message de maintenance actuel
     */
    public static function getMessage(): string
    {
        $sql = "SELECT message FROM maintenance_mode WHERE actif = 1 LIMIT 1";
        $stmt = self::raw($sql);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row['message'] ?? self::MESSAGE_DEFAUT;
    }

    /**
     * Retourne la configuration de maintenance active
     */
    public static function getConfig(): ?self
    {
        $sql = "SELECT * FROM maintenance_mode WHERE actif = 1 LIMIT 1";
        $stmt = self::raw($sql);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES DE GESTION =====

    /**
     * Active le mode maintenance immédiatement
     */
    public static function activer(string $message = self::MESSAGE_DEFAUT): void
    {
        // Désactiver les anciens enregistrements
        $sql = "UPDATE maintenance_mode SET actif = 0";
        self::raw($sql);

        // Créer le nouvel enregistrement
        $maintenance = new self([
            'actif' => true,
            'message' => $message,
            'debut_maintenance' => date('Y-m-d H:i:s'),
        ]);
        $maintenance->save();
    }

    /**
     * Désactive le mode maintenance
     */
    public static function desactiver(): void
    {
        $sql = "UPDATE maintenance_mode SET actif = 0, fin_maintenance = NOW()";
        self::raw($sql);
    }

    /**
     * Planifie une maintenance
     */
    public static function planifier(
        \DateTime $debut,
        \DateTime $fin,
        string $message = self::MESSAGE_MISE_A_JOUR
    ): self {
        // Désactiver les anciens enregistrements
        $sql = "UPDATE maintenance_mode SET actif = 0";
        self::raw($sql);

        $maintenance = new self([
            'actif' => true,
            'message' => $message,
            'debut_maintenance' => $debut->format('Y-m-d H:i:s'),
            'fin_maintenance' => $fin->format('Y-m-d H:i:s'),
        ]);
        $maintenance->save();
        return $maintenance;
    }

    /**
     * Retourne le temps restant avant la fin (en secondes)
     */
    public function tempsRestant(): ?int
    {
        if (empty($this->fin_maintenance)) {
            return null;
        }

        $fin = strtotime($this->fin_maintenance);
        return max(0, $fin - time());
    }
}
