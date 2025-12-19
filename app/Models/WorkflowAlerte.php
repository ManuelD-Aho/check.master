<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowAlerte
 * 
 * Gère les alertes de délai du workflow.
 * Table: workflow_alertes
 */
class WorkflowAlerte extends Model
{
    protected string $table = 'workflow_alertes';
    protected string $primaryKey = 'id_alerte';
    protected array $fillable = [
        'dossier_id',
        'etat_id',
        'type_alerte',
        'envoyee',
        'envoyee_le',
    ];

    /**
     * Types d'alertes
     */
    public const TYPE_50_POURCENT = '50_pourcent';
    public const TYPE_80_POURCENT = '80_pourcent';
    public const TYPE_100_POURCENT = '100_pourcent';

    // ===== RELATIONS =====

    /**
     * Retourne le dossier étudiant
     */
    public function dossier(): ?DossierEtudiant
    {
        return $this->belongsTo(DossierEtudiant::class, 'dossier_id', 'id_dossier');
    }

    /**
     * Retourne l'état
     */
    public function etat(): ?WorkflowEtat
    {
        return $this->belongsTo(WorkflowEtat::class, 'etat_id', 'id_etat');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les alertes d'un dossier
     * @return self[]
     */
    public static function pourDossier(int $dossierId): array
    {
        return self::where(['dossier_id' => $dossierId]);
    }

    /**
     * Retourne les alertes non envoyées
     * @return self[]
     */
    public static function nonEnvoyees(): array
    {
        return self::where(['envoyee' => false]);
    }

    /**
     * Vérifie si une alerte existe déjà
     */
    public static function existe(int $dossierId, int $etatId, string $typeAlerte): bool
    {
        return self::count([
            'dossier_id' => $dossierId,
            'etat_id' => $etatId,
            'type_alerte' => $typeAlerte,
        ]) > 0;
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si l'alerte a été envoyée
     */
    public function estEnvoyee(): bool
    {
        return (bool) $this->envoyee;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée une alerte si elle n'existe pas déjà
     */
    public static function creerSiInexistante(int $dossierId, int $etatId, string $typeAlerte): ?self
    {
        if (self::existe($dossierId, $etatId, $typeAlerte)) {
            return null;
        }

        $alerte = new self([
            'dossier_id' => $dossierId,
            'etat_id' => $etatId,
            'type_alerte' => $typeAlerte,
            'envoyee' => false,
        ]);
        $alerte->save();
        return $alerte;
    }

    /**
     * Marque l'alerte comme envoyée
     */
    public function marquerEnvoyee(): void
    {
        $this->envoyee = true;
        $this->envoyee_le = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Génère les alertes pour les dossiers en retard
     */
    public static function genererAlertes(): array
    {
        $alertesCrees = [];

        // Récupérer les dossiers avec date limite
        $sql = "SELECT de.id_dossier, de.etat_actuel_id, de.date_entree_etat, de.date_limite_etat
                FROM dossiers_etudiants de
                WHERE de.date_limite_etat IS NOT NULL
                AND de.date_limite_etat > NOW()";

        $stmt = self::raw($sql, []);
        $dossiers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($dossiers as $dossier) {
            $debut = strtotime($dossier['date_entree_etat']);
            $fin = strtotime($dossier['date_limite_etat']);
            $now = time();

            if ($debut >= $fin) {
                continue;
            }

            $total = $fin - $debut;
            $ecoule = $now - $debut;
            $pourcentage = ($ecoule / $total) * 100;

            $dossierId = (int) $dossier['id_dossier'];
            $etatId = (int) $dossier['etat_actuel_id'];

            // Alerte 50%
            if ($pourcentage >= 50) {
                $alerte = self::creerSiInexistante($dossierId, $etatId, self::TYPE_50_POURCENT);
                if ($alerte) {
                    $alertesCrees[] = $alerte;
                }
            }

            // Alerte 80%
            if ($pourcentage >= 80) {
                $alerte = self::creerSiInexistante($dossierId, $etatId, self::TYPE_80_POURCENT);
                if ($alerte) {
                    $alertesCrees[] = $alerte;
                }
            }

            // Alerte 100%
            if ($pourcentage >= 100) {
                $alerte = self::creerSiInexistante($dossierId, $etatId, self::TYPE_100_POURCENT);
                if ($alerte) {
                    $alertesCrees[] = $alerte;
                }
            }
        }

        return $alertesCrees;
    }

    /**
     * Supprime les alertes d'un dossier (après changement d'état)
     */
    public static function supprimerPourDossier(int $dossierId): int
    {
        $sql = "DELETE FROM workflow_alertes WHERE dossier_id = :id";
        $stmt = self::raw($sql, ['id' => $dossierId]);
        return $stmt->rowCount();
    }
}
