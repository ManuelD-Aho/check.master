<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NotificationTemplate
 * 
 * Template pour les notifications (Email, SMS, Messagerie).
 * Table: notification_templates
 */
class NotificationTemplate extends Model
{
    protected string $table = 'notification_templates';
    protected string $primaryKey = 'id_template';
    protected array $fillable = [
        'code_template',
        'canal',
        'sujet',
        'corps',
        'variables_json',
        'actif',
    ];

    /**
     * Canaux de notification
     */
    public const CANAL_EMAIL = 'Email';
    public const CANAL_SMS = 'SMS';
    public const CANAL_MESSAGERIE = 'Messagerie';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un template par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_template' => $code, 'actif' => true]);
    }

    /**
     * Retourne tous les templates actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les templates par canal
     * @return self[]
     */
    public static function parCanal(string $canal): array
    {
        return self::where(['canal' => $canal, 'actif' => true]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne les variables attendues
     */
    public function getVariables(): array
    {
        if (empty($this->variables_json)) {
            return [];
        }
        return json_decode($this->variables_json, true) ?? [];
    }

    /**
     * Définit les variables attendues
     */
    public function setVariables(array $variables): void
    {
        $this->variables_json = json_encode($variables);
    }

    /**
     * Compile le sujet avec les variables
     */
    public function compilerSujet(array $variables): string
    {
        $sujet = $this->sujet ?? '';
        foreach ($variables as $key => $value) {
            $sujet = str_replace("{{$key}}", (string) $value, $sujet);
        }
        return $sujet;
    }

    /**
     * Compile le corps avec les variables
     */
    public function compilerCorps(array $variables): string
    {
        $corps = $this->corps ?? '';
        foreach ($variables as $key => $value) {
            $corps = str_replace("{{$key}}", (string) $value, $corps);
        }
        return $corps;
    }

    /**
     * Active le template
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive le template
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }

    /**
     * Crée un nouveau template
     */
    public static function creer(
        string $code,
        string $canal,
        string $sujet,
        string $corps,
        array $variables = []
    ): self {
        $template = new self([
            'code_template' => $code,
            'canal' => $canal,
            'sujet' => $sujet,
            'corps' => $corps,
            'variables_json' => json_encode($variables),
            'actif' => true,
        ]);
        $template->save();
        return $template;
    }
}
