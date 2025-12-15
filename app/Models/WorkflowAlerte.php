<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowAlerte
 * 
 * Alertes liées aux délais du workflow.
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

    /**
     * Crée une alerte si elle n'existe pas
     */
    public static function creerSiAbsente(int $dossierId, int $etatId, string $type): self
    {
        $existing = self::firstWhere([
            'dossier_id' => $dossierId,
            'etat_id' => $etatId,
            'type_alerte' => $type,
        ]);

        if ($existing !== null) {
            return $existing;
        }

        $alerte = new self([
            'dossier_id' => $dossierId,
            'etat_id' => $etatId,
            'type_alerte' => $type,
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
     * Retourne les alertes non envoyées
     *
     * @return self[]
     */
    public static function nonEnvoyees(): array
    {
        return self::where(['envoyee' => false]);
    }

    /**
     * Retourne les alertes d'un dossier
     *
     * @return self[]
     */
    public static function pourDossier(int $dossierId): array
    {
        return self::where(['dossier_id' => $dossierId]);
    }
}
